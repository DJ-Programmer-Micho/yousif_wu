<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sender extends Model
{
    use SoftDeletes;

    public const STATUS_EXECUTED = 'Executed';
    public const STATUS_PENDING  = 'Pending';
    public const STATUS_REJECTED = 'Rejected';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'country',
        'amount',
        'tax',
        'total',
        'r_first_name',
        'r_last_name',
        'r_phone',
        'mtcn',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax'    => 'decimal:2',
        'total'  => 'decimal:2',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
