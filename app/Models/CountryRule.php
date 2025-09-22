<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryRule extends Model
{
    protected $fillable = ['country_id','rule'];

    protected $casts = [
        'rule' => 'boolean', // make sure true/false is handled cleanly
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
