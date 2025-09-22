<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiverBalance extends Model
{
    protected $fillable = ['user_id','amount','status','receiver_id','admin_id','note'];

    public function user(): BelongsTo     { return $this->belongsTo(User::class); }
    public function receiver(): BelongsTo { return $this->belongsTo(Receiver::class, 'receiver_id'); }
    public function admin(): BelongsTo    { return $this->belongsTo(User::class, 'admin_id'); }
}