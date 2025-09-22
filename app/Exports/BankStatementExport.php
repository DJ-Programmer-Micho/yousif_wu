<?php

namespace App\Exports;

use App\Models\Sender;
use App\Models\Receiver;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class BankStatementExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting
{
    public function __construct(
        protected string $tab,              // 'senders' | 'receivers'
        protected array  $filters,          // all Livewire filters
        protected ?Authenticatable $user,   // current user
        protected bool $isAdmin             // role check
    ) {}

    /** Build the filtered query (no pagination) */
    public function query()
    {
        return $this->tab === 'senders'
            ? $this->buildSendersQuery()
            : $this->buildReceiversQuery();
    }

    /** Column headers */
    public function headings(): array
    {
        if ($this->tab === 'senders') {
            return ['Date', 'Status', 'MTCN', 'Sender', 'Phone', 'Country', 'Amount ($)', 'Fee ($)', 'Total ($)', 'Receiver', 'Register'];
        }
        return ['Date', 'Status', 'MTCN', 'Receiver', 'Phone', 'Address', 'Amount (IQD)', 'Register'];
    }

    /** Row mapping */
    public function map($row): array
    {
        if ($this->tab === 'senders') {
            return [
                optional($row->created_at)->timezone('Asia/Baghdad')->format('Y-m-d H:i'),
                $row->status,
                $this->formatMtcn($row->mtcn),
                trim(($row->first_name ?? '').' '.($row->last_name ?? '')),
                "'".$row->phone,
                $row->country,
                (float) $row->amount,
                (float) $row->tax,
                (float) $row->total,
                trim(($row->r_first_name ?? '').' '.($row->r_last_name ?? '')),
                optional($row->user)->name,
            ];
        }

        return [
            optional($row->created_at)->timezone('Asia/Baghdad')->format('Y-m-d H:i'),
            $row->status,
            $this->formatMtcn($row->mtcn),
            trim(($row->first_name ?? '').' '.($row->last_name ?? '')),
            "'".$row->phone,
            $row->address,
            (float) $row->amount_iqd, // change if your column is different
            optional($row->user)->name,
        ];
    }

    /** Number formats (only numerics) */
    public function columnFormats(): array
    {
        return $this->tab === 'senders'
            ? [
                'G' => NumberFormat::FORMAT_NUMBER_00, // amount
                'H' => NumberFormat::FORMAT_NUMBER_00, // fee
                'I' => NumberFormat::FORMAT_NUMBER_00, // total
                'E' => NumberFormat::FORMAT_TEXT,
              ]
            : [
                'G' => NumberFormat::FORMAT_NUMBER,    // IQD
                'E' => NumberFormat::FORMAT_TEXT,
              ];
    }

    // ----------------- helpers -----------------

    protected function buildSendersQuery(): Builder
    {
        $q = Sender::query()->with('user');

        // scope to owner unless admin
        if (!$this->isAdmin && $this->user) {
            $q->where('user_id', $this->user->getAuthIdentifier());
        }
        $f = $this->filters;

        // filters
        if (!empty($f['status']))  $q->where('status', $f['status']);
        if (!empty($f['country'])) $q->where('country', $f['country']);
        if ($this->isAdmin && !empty($f['registerId'])) $q->where('user_id', (int) $f['registerId']);

        if (!empty($f['dateFrom'])) $q->whereDate('created_at', '>=', Carbon::parse($f['dateFrom']));
        if (!empty($f['dateTo']))   $q->whereDate('created_at', '<=', Carbon::parse($f['dateTo']));

        if ($f['amountFrom'] !== '' && $f['amountFrom'] !== null) $q->where('amount', '>=', (float)$f['amountFrom']);
        if ($f['amountTo']   !== '' && $f['amountTo']   !== null) $q->where('amount', '<=', (float)$f['amountTo']);

        if (!empty($f['q'])) {
            $term   = $this->escapeLike($f['q']);
            $digits = preg_replace('/\D+/', '', $f['q']);
            $q->where(function ($w) use ($term, $digits) {
                $w->where('mtcn', 'like', $term);
                if ($digits !== '') $w->orWhere('mtcn', 'like', '%'.$digits.'%');

                $w->orWhere('first_name', 'like', $term)
                  ->orWhere('last_name',  'like', $term)
                  ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", [$term])
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('r_first_name', 'like', $term)
                  ->orWhere('r_last_name',  'like', $term)
                  ->orWhereRaw("CONCAT(COALESCE(r_first_name,''),' ',COALESCE(r_last_name,'')) LIKE ?", [$term])
                  ->orWhere('r_phone', 'like', $term);
            });
        }

        // sorting
        $sortBy = $f['sortBy'] ?? 'created_at';
        $sortDir = ($f['sortDirection'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $q->orderBy($sortBy, $sortDir);

        return $q->select([
            'id','user_id','status','mtcn','first_name','last_name','phone','country',
            'amount','tax','total','r_first_name','r_last_name','created_at'
        ]);
    }

    protected function buildReceiversQuery(): Builder
    {
        $q = Receiver::query()->with('user');

        if (!$this->isAdmin && $this->user) {
            $q->where('user_id', $this->user->getAuthIdentifier());
        }
        $f = $this->filters;

        if (!empty($f['status'])) $q->where('status', $f['status']);
        if ($this->isAdmin && !empty($f['registerId'])) $q->where('user_id', (int) $f['registerId']);

        if (!empty($f['dateFrom'])) $q->whereDate('created_at', '>=', Carbon::parse($f['dateFrom']));
        if (!empty($f['dateTo']))   $q->whereDate('created_at', '<=', Carbon::parse($f['dateTo']));

        if ($f['amountFrom'] !== '' && $f['amountFrom'] !== null) $q->where('amount_iqd', '>=', (float)$f['amountFrom']);
        if ($f['amountTo']   !== '' && $f['amountTo']   !== null) $q->where('amount_iqd', '<=', (float)$f['amountTo']);

        if (!empty($f['q'])) {
            $term = $this->escapeLike($f['q']);
            $q->where(function ($w) use ($term) {
                $w->where('first_name','like',$term)
                  ->orWhere('last_name','like',$term)
                  ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", [$term])
                  ->orWhere('phone','like',$term)
                  ->orWhere('address','like',$term)
                  ->orWhere('mtcn','like',$term);
            });
        }

        $sortBy = $f['sortBy'] ?? 'created_at';
        $sortDir = ($f['sortDirection'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $q->orderBy($sortBy, $sortDir);

        return $q->select([
            'id','user_id','status','mtcn','first_name','last_name','phone','address','amount_iqd','created_at'
        ]);
    }

    protected function escapeLike(string $t): string
    {
        $t = trim($t);
        return '%'.str_replace(['%','_'], ['\%','\_'], $t).'%';
    }

    protected function formatMtcn(?string $v): string
    {
        $v = (string) $v;
        return preg_match('/^\d{10}$/', $v)
            ? substr($v,0,3).'-'.substr($v,3,3).'-'.substr($v,6,4)
            : $v;
    }
}
