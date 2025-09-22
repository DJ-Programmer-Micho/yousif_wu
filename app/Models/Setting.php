<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = ['key','value'];
    protected $casts = ['value' => 'array'];

    public static function get(string $key, $default = null) {
        return optional(static::query()->where('key',$key)->first())->value ?? $default;
    }

    public static function put(string $key, $value): void {
        static::query()->updateOrCreate(['key'=>$key], ['value'=>$value]);
    }
}
