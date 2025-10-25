<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        'country_id', 'code', 'en_name', 'ar_name', 'ku_name',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
