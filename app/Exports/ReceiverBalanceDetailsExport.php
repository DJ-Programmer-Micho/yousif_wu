<?php

namespace App\Exports;

use App\Models\ReceiverBalance;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReceiverBalanceDetailsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        protected int $userId,
        protected array $filters,                // ['mode','dateFrom','dateTo']
        protected ?Authenticatable $user,
        protected bool $isAdmin
    ) {}

    public function query(): Builder
    {
        $q = ReceiverBalance::query()
            ->with(['receiver:id,first_name,last_name', 'admin:id,name'])
            ->where('user_id', $this->userId);

        // mode: all | incoming | outgoing
        if (($this->filters['mode'] ?? 'all') === 'incoming') {
            $q->where('status', 'Incoming');
        } elseif (($this->filters['mode'] ?? 'all') === 'outgoing') {
            $q->where('status', 'Outgoing');
        }

        // date range (inclusive)
        if (!empty($this->filters['dateFrom'])) {
            $q->whereDate('created_at', '>=', $this->filters['dateFrom']);
        }
        if (!empty($this->filters['dateTo'])) {
            $q->whereDate('created_at', '<=', $this->filters['dateTo']);
        }

        // auth guard
        if (!$this->isAdmin && $this->user && $this->user->getAuthIdentifier() !== $this->userId) {
            $q->whereRaw('1=0');
        }

        return $q->latest('id');
    }

    public function headings(): array
    {
        return [
            'Date (Asia/Baghdad)', 'Status', 'Amount (IQD)',
            'Receiver (person)', 'Admin', 'Note',
        ];
    }

    public function map($row): array
    {
        return [
            optional($row->created_at)->timezone('Asia/Baghdad')->format('Y-m-d H:i'),
            (string) $row->status,
            (int) $row->amount,
            $row->receiver ? trim(($row->receiver->first_name ?? '').' '.($row->receiver->last_name ?? '')) : '—',
            $row->admin->name ?? '—',
            $row->note ?? '—',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER, // Amount (IQD) integer
        ];
    }
}
