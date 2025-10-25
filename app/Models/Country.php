<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'iso_code', 'en_name', 'ar_name', 'ku_name', 'flag_path', 'flagx2_path',
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }
}