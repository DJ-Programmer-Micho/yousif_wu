<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BankStatementExport;

class BankStatementExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $tab = in_array($request->get('tab'), ['senders','receivers'], true) ? $request->get('tab') : 'senders';

        // Collect all filters you use in Livewire
        $filters = [
            'q'             => (string) $request->get('q', ''),
            'status'        => (string) $request->get('status', ''),
            'country'       => (string) $request->get('country', ''),
            'registerId'    => $request->get('registerId'),
            'dateFrom'      => (string) $request->get('dateFrom', ''),
            'dateTo'        => (string) $request->get('dateTo', ''),
            'amountFrom'    => (string) $request->get('amountFrom', ''),
            'amountTo'      => (string) $request->get('amountTo', ''),
            'sortBy'        => (string) $request->get('sortBy', 'created_at'),
            'sortDirection' => (string) $request->get('sortDirection', 'desc'),
        ];

        $user    = $request->user();
        $isAdmin = ((int) $user->role) === 1; // align with your app

        $file = sprintf('bank-statement_%s_%s.xlsx', $tab, now('Asia/Baghdad')->format('Ymd_His'));

        return Excel::download(new BankStatementExport($tab, $filters, $user, $isAdmin), $file);
    }
}
