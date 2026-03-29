<?php

namespace App\Http\Livewire\General;

use App\Models\Announcement;
use Livewire\Component;

class AnnouncementShowLivewire extends Component
{
    public int $limit = 6;
    public string $role = 'Register';
    public int $newWithinDays = 7;

    public function mount($limit = 6, $role = 'Register'): void
    {
        $this->limit = (int) $limit;
        $this->role = (string) $role;
    }

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