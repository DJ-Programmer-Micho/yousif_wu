<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CountryTax extends Model
{
    protected $fillable = ['country_id','tax_bracket_set_id'];

    public function country() { return $this->belongsTo(Country::class); }
    public function set()     { return $this->belongsTo(TaxBracketSet::class, 'tax_bracket_set_id'); }
}