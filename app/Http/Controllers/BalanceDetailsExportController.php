<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SenderBalanceDetailsExport;
use App\Exports\ReceiverBalanceDetailsExport;

class BalanceDetailsExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $type   = in_array($request->get('type'), ['sender','receiver'], true) ? $request->get('type') : 'sender';
        $userId = (int) $request->get('userId');

        // filters coming from Livewire components
        $filters = [
            'mode'     => (string) $request->get('mode', 'all'),      // all|incoming|outgoing
            'dateFrom' => (string) $request->get('dateFrom', ''),     // YYYY-MM-DD
            'dateTo'   => (string) $request->get('dateTo', ''),       // YYYY-MM-DD
        ];

        $user    = $request->user();
        $isAdmin = ((int) $user->role) === 1;

        $ts   = now('Asia/Baghdad')->format('Ymd_His');
        $file = sprintf('%s-balance-details_u%s_%s.xlsx', $type, $userId, $ts);

        if ($type === 'sender') {
            return Excel::download(new SenderBalanceDetailsExport($userId, $filters, $user, $isAdmin), $file);
        }

        return Excel::download(new ReceiverBalanceDetailsExport($userId, $filters, $user, $isAdmin), $file);
    }
}
