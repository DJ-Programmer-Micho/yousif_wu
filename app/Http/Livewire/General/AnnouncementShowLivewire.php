<?php

namespace App\Http\Livewire\General;

use App\Models\Announcement;
use Livewire\Component;

class AnnouncementShowLivewire extends Component
{
    /** How many to show */
    public int $limit = 6;

    /** Role filter (default: Register) */
    public string $role = 'Register';

    /** Show “NEW” if within X days */
    public int $newWithinDays = 7;

    public function getRowsProperty()
    {
        return Announcement::query()
            ->shown()
            ->inWindow()
            ->forRole($this->role)
            ->latest()
            ->take($this->limit)
            ->get();
    }

    public function render()
    {
        return view('components.general.announcement-feed', [
            'rows' => $this->rows,
            'newCutoff' => now()->subDays($this->newWithinDays),
        ]);
    }
}
