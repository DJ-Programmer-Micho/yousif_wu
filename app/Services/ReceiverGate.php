<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Contracts\Auth\Authenticatable;

class ReceiverGate
{
    // modes: none, all, by_register
    public static function mode(): string {
        return (string) (Setting::get('receiver.mode', 'none'));
    }

    public static function blockedIds(): array {
        return (array) (Setting::get('receiver.blocked_ids', []));
    }

    public static function isBlockedFor(?Authenticatable $user): bool {
        $mode = static::mode();
        if ($mode === 'all') return true;
        if ($mode === 'by_register' && $user) {
            return in_array((int)$user->id, static::blockedIds(), true);
        }
        return false; // mode none
    }
}
