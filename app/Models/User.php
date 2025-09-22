<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'g_password',
        'role',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'g_password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    public function profile(): HasOne {
        return $this->hasOne(UserProfile::class);
    }

    public function senders(): HasMany {
        return $this->hasMany(Sender::class);
    }

    public function receivers(): HasMany {
        return $this->hasMany(Receiver::class);
    }

    public function senderBalances() { return $this->hasMany(\App\Models\SenderBalance::class); }
    public function receiverBalances() { return $this->hasMany(\App\Models\ReceiverBalance::class); }

    public function senderBalanceRemaining(): float
    {
        $in  = $this->senderBalances()->where('status','Incoming')->sum('amount');
        $out = $this->senderBalances()->where('status','Outgoing')->sum('amount');
        return (float)$in - (float)$out;
    }

    public function receiverBalanceRunning(): int
    {
        $in  = (int)$this->receiverBalances()->where('status','Incoming')->sum('amount');
        $out = (int)$this->receiverBalances()->where('status','Outgoing')->sum('amount');
        return $in - $out;
    }
}
