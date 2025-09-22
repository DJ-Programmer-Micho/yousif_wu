<?php

namespace App\Services;

use App\Models\SenderBalance;
use App\Models\ReceiverBalance;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    // ========== SENDERS (USD) ==========
    public function getSenderRemaining(int $userId): float
    {
        return (float) SenderBalance::where('user_id',$userId)
            ->selectRaw("SUM(CASE WHEN status='Incoming' THEN amount ELSE 0 END) - SUM(CASE WHEN status='Outgoing' THEN amount ELSE 0 END) as rem")
            ->value('rem') ?? 0.0;
    }

    public function topUpSender(int $adminId, int $userId, float $amount, ?string $note = null): void
    {
        DB::transaction(function() use ($adminId,$userId,$amount,$note){
            SenderBalance::create([
                'user_id' => $userId,
                'amount'  => $amount,
                'status'  => 'Incoming',
                'admin_id'=> $adminId,
                'note'    => $note,
            ]);
        });
    }

    // Deduct on creation (hold). If later Rejected → refund (create Incoming of same amount).
    public function chargeSenderOnCreate(int $userId, float $totalUsd, int $senderId): void
    {
        DB::transaction(function() use ($userId,$totalUsd,$senderId){
            // pessimistic lock to be safe in high concurrency
            DB::table('sender_balances')->where('user_id',$userId)->lockForUpdate()->get();
            $remaining = $this->getSenderRemaining($userId);
            if ($totalUsd > $remaining + 1e-6) {
                throw new \RuntimeException('Insufficient balance.');
            }
            SenderBalance::create([
                'user_id'  => $userId,
                'amount'   => $totalUsd,
                'status'   => 'Outgoing',
                'sender_id'=> $senderId,
                'note'     => 'Sender created (hold)',
            ]);
        });
    }

    public function refundSenderOnReject(int $userId, float $totalUsd, int $senderId): void
    {
        DB::transaction(function() use ($userId,$totalUsd,$senderId){
            SenderBalance::create([
                'user_id'  => $userId,
                'amount'   => $totalUsd,
                'status'   => 'Incoming',
                'sender_id'=> $senderId,
                'note'     => 'Sender rejected (refund)',
            ]);
        });
    }

    // ========== RECEIVERS (IQD) ==========
    public function creditReceiver(int $userId, int $receiverId, int $amountIqd): void
    {
        DB::transaction(function() use ($userId,$receiverId,$amountIqd){
            ReceiverBalance::create([
                'user_id'    => $userId,
                'receiver_id'=> $receiverId,
                'amount'     => $amountIqd,
                'status'     => 'Incoming',
                'note'       => 'Receiver credited',
            ]);
        });
    }

    // Reset to zero = book Outgoing equal to current running balance (admin only)
    public function resetReceiverToZero(int $adminId, int $userId): void
    {
        DB::transaction(function() use ($adminId,$userId){
            $running = (int) ReceiverBalance::where('user_id',$userId)
                ->selectRaw("SUM(CASE WHEN status='Incoming' THEN amount ELSE 0 END) - SUM(CASE WHEN status='Outgoing' THEN amount ELSE 0 END) as r")
                ->value('r') ?? 0;
            if ($running > 0) {
                ReceiverBalance::create([
                    'user_id' => $userId,
                    'amount'  => $running,
                    'status'  => 'Outgoing',
                    'admin_id'=> $adminId,
                    'note'    => 'Admin reset to zero',
                ]);
            }
        });
    }
}
