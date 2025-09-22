<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GeneralCountryTax extends Model
{
    protected $table = 'general_country_taxes';
    protected $fillable = ['brackets_json'];
    protected $casts = ['brackets_json' => 'array'];
}