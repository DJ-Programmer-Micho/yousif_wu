<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'body',
        'is_visible',
        'show_from',
        'show_until',
        'audience_roles',
    ];

    protected $casts = [
        'is_visible'     => 'boolean',
        'show_from'      => 'datetime',
        'show_until'     => 'datetime',
        'audience_roles' => 'array',
    ];

    /** Creator (admin) */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Scope: visible flag on */
    public function scopeShown(Builder $q): Builder
    {
        return $q->where('is_visible', true);
    }

    /** Scope: within optional time window */
    public function scopeInWindow(Builder $q): Builder
    {
        $now = now();

        return $q
            ->where(function ($qq) use ($now) {
                $qq->whereNull('show_from')->orWhere('show_from', '<=', $now);
            })
            ->where(function ($qq) use ($now) {
                $qq->whereNull('show_until')->orWhere('show_until', '>=', $now);
            });
    }

    /** Scope: audience targeting by role (defaults to everyone if null) */
    public function scopeForRole(Builder $q, string $role): Builder
    {
        return $q->where(function ($qq) use ($role) {
            $qq->whereNull('audience_roles')
               ->orWhereJsonContains('audience_roles', $role);
        });
    }
}
