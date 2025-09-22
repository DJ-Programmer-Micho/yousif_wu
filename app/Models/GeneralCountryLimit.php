<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GeneralCountryLimit extends Model
{
    protected $table = 'general_country_limits';
    protected $fillable = ['min_value','max_value'];
}