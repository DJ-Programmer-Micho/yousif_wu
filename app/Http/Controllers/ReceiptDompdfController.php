<?php

namespace App\Http\Controllers;

use App\Models\Receiver;
use App\Models\Sender;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use iio\libmergepdf\Merger;

class ReceiptDompdfController extends Controller
{
    public function show(Sender $sender, string $type, Request $request)
    {
        // $this->authorize('view', $sender); // admin or owner

        // Build data
        $mtcnFormatted = (preg_match('/^\d{10}$/', (string)$sender->mtcn))
            ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', (string)$sender->mtcn)
            : (string)$sender->mtcn;


        
        $data = [
            'sender_name' => trim($sender->first_name.' '.$sender->last_name),
            'phone' => $sender->phone,
            'receiver_name' => trim(($sender->r_first_name ?? '').' '.($sender->r_last_name ?? '')),
            'r_phone' => $sender->r_phone ?? null,
            'date_now' => now('Asia/Baghdad')->format('Y-m-d H:i'),
            'date' => $sender->created_at->clone()->setTimezone('Asia/Baghdad')->format('Y-m-d H:i \G\M\T\+\3'),
            'state_id' => $sender->state->en_name ?? 'N/A',
            'address' => $sender->address,
            'country' => $sender->country,
            'amount' => (float)$sender->amount,
            'fee'    => (float)$sender->tax,
            'total'  => (float)$sender->total,
            'mtcn' => $mtcnFormatted,
        ];

        // --- EITHER: Blade view (recommended) ---
        if ($type === 'both') {
            $pdfCustomer = Pdf::loadView('receipts.customer', $data)->setPaper('a4','portrait')->setWarnings(false);
            $pdfAgent    = Pdf::loadView('receipts.agent', $data)->setPaper('a4','portrait')->setWarnings(false);

            // Merge the binary PDFs (no CSS conflicts)
            $merger = new Merger();
            $merger->addRaw($pdfCustomer->output());
            $merger->addRaw($pdfAgent->output());
            $combined = $merger->merge();

            return response($combined, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="receipts-'.$sender->mtcn.'.pdf"',
            ]);
        }

        $view = $type === 'agent' ? 'receipts.agent' : 'receipts.customer';
        return Pdf::loadView($view, $data)->setPaper('a4','portrait')->setWarnings(false)
             ->stream("{$type}-receipt-{$sender->mtcn}.pdf");
    }

    public function senderShow(Sender $sender, string $type, Request $request)
    {
        // $this->authorize('view', $sender); // admin or owner

        // Build data
        $mtcnFormatted = (preg_match('/^\d{10}$/', (string)$sender->mtcn))
            ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', (string)$sender->mtcn)
            : (string)$sender->mtcn;

        
        $data = [
            'sender_name' => trim($sender->first_name.' '.$sender->last_name),
            'phone' => $sender->phone,
            'receiver_name' => trim(($sender->r_first_name ?? '').' '.($sender->r_last_name ?? '')),
            'r_phone' => $sender->r_phone ?? null,
            'date_now' => now('Asia/Baghdad')->format('Y-m-d H:i'),
            'date' => $sender->created_at->clone()->setTimezone('Asia/Baghdad')->format('Y-m-d H:i \G\M\T\+\3'),
            'state_id' => $sender->state->en_name ?? 'N/A',
            'address' => $sender->address,
            'country' => $sender->country,
            'amount' => (float)$sender->amount,
            'fee'    => (float)$sender->tax,
            'total'  => (float)$sender->total,
            'mtcn' => $mtcnFormatted,
            'payout' => $sender->payouts[0],
        ];

        // --- EITHER: Blade view (recommended) ---
        if ($type === 'both') {
            $pdfCustomer = Pdf::loadView('receipts.executed-customer', $data)->setPaper('a4','portrait')->setWarnings(false);
            $pdfAgent    = Pdf::loadView('receipts.executed-agent', $data)->setPaper('a4','portrait')->setWarnings(false);

            // Merge the binary PDFs (no CSS conflicts)
            $merger = new Merger();
            $merger->addRaw($pdfCustomer->output());
            $merger->addRaw($pdfAgent->output());
            $combined = $merger->merge();

            return response($combined, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="receipts-'.$sender->mtcn.'.pdf"',
            ]);
        }

        $view = $type === 'agent' ? 'receipts.executed-agent' : 'receipts.executed-customer';
        return Pdf::loadView($view, $data)->setPaper('a4','portrait')->setWarnings(false)
             ->stream("{$type}-receipt-{$sender->id}.pdf");
    }

    public function receiverShow(Receiver $receiver, string $type, Request $request)
    {
        // Build data
        $data = [
            'receiver_name' => trim($receiver->first_name.' '.$receiver->last_name),
            'phone' => $receiver->phone,
            'date_now' => now('Asia/Baghdad')->format('Y-m-d H:i'),
            'date' => $receiver->created_at->clone()->setTimezone('Asia/Baghdad')->format('Y-m-d H:i \G\M\T\+\3'),
            'address' => $receiver->address,
            'total' => $receiver->amount_iqd,
            'mtcn' => $receiver->mtcn,
        ];

        if ($type === 'both') {
            $pdfCustomer = Pdf::loadView('receipts.receiver-customer', $data)->setPaper('a4','portrait')->setWarnings(false);
            $pdfAgent    = Pdf::loadView('receipts.receiver-agent', $data)->setPaper('a4','portrait')->setWarnings(false);

            // Merge the binary PDFs (no CSS conflicts)
            $merger = new Merger();
            $merger->addRaw($pdfCustomer->output());
            $merger->addRaw($pdfAgent->output());
            $combined = $merger->merge();

            return response($combined, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="receipts-'.$receiver->first_name.'-'.$receiver->last_name.'.pdf"',
            ]);
        }

        $view = $type === 'agent' ? 'receipts.receiver-agent' : 'receipts.receiver-customer';
        return Pdf::loadView($view, $data)->setPaper('a4','portrait')->setWarnings(false)
             ->stream("{$type}-receipt-{$receiver->mtcn}.pdf");
    

    }

    public function executedReceiverShow(Receiver $receiver, string $type, Request $request)
    {
        $mtcnFormatted = (preg_match('/^\d{10}$/', (string)$receiver->mtcn))
            ? preg_replace('/(\d{3})(\d{3})(\d{4})/', '$1-$2-$3', (string)$receiver->mtcn)
            : (string)$receiver->mtcn;

        // Build data
        $data = [
            'receiver_name' => trim($receiver->first_name.' '.$receiver->last_name),
            'phone' => $receiver->phone,
            'date_now' => now('Asia/Baghdad')->format('Y-m-d H:i'),
            'date' => $receiver->created_at->clone()->setTimezone('Asia/Baghdad')->format('Y-m-d H:i \G\M\T\+\3'),
            'address' => $receiver->address,
            'total' => $receiver->amount_iqd,
            'mtcn' => $mtcnFormatted,
        ];

        if ($type === 'both') {
            $pdfCustomer = Pdf::loadView('receipts.executed-receiver-customer', $data)->setPaper('a4','portrait')->setWarnings(false);
            $pdfAgent    = Pdf::loadView('receipts.executed-receiver-agent', $data)->setPaper('a4','portrait')->setWarnings(false);

            // Merge the binary PDFs (no CSS conflicts)
            $merger = new Merger();
            $merger->addRaw($pdfCustomer->output());
            $merger->addRaw($pdfAgent->output());
            $combined = $merger->merge();

            return response($combined, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="receipts-'.$receiver->first_name.'-'.$receiver->last_name.'.pdf"',
            ]);
        }

        $view = $type === 'agent' ? 'receipts.executed-receiver-agent' : 'receipts.executed-receiver-customer';
        return Pdf::loadView($view, $data)->setPaper('a4','portrait')->setWarnings(false)
             ->stream("{$type}-receipt-{$receiver->mtcn}.pdf");
    

    }
}
