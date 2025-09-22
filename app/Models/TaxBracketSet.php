<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TaxBracketSet extends Model
{
    protected $fillable = ['name','brackets_json'];
    protected $casts = ['brackets_json' => 'array']; // [[min,max,fee], ...]
}