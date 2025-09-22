<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receiver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'mtcn',
        'first_name',
        'last_name',
        'phone',
        'address',
        'amount_iqd',     // store amount in IQD
        'identification', // nullable for now
        'status'
    ];

    protected $casts = [
        'amount_iqd'    => 'decimal:2',
        'identification'=> 'array', // keep flexible; can be null
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
