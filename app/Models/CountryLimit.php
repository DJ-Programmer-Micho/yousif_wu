<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CountryLimit extends Model
{
    protected $fillable = ['country_id','min_value','max_value'];

    public function country() { return $this->belongsTo(Country::class); }
}