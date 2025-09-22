<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'phone',
        'avatar',
        'country',
        'state',
        'city',
        'address'
    ];
    
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
