<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SenderBalance extends Model
{
    protected $fillable = ['user_id','amount','status','sender_id','admin_id','note'];

    public function user(): BelongsTo   { return $this->belongsTo(User::class); }
    public function sender(): BelongsTo { return $this->belongsTo(Sender::class, 'sender_id'); }
    public function admin(): BelongsTo  { return $this->belongsTo(User::class, 'admin_id'); }
}