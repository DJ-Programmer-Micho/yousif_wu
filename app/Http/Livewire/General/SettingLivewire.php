<?php

namespace App\Http\Livewire\General;

use Livewire\Component;
use App\Models\User;
use App\Models\Setting;

class SettingLivewire extends Component
{
    /** UI state */
    public bool  $isAdmin      = false;
    public bool  $disableAll   = false;     // global override
    public array $rows         = [];        // [{id,name,disabled}]
    public int   $totalRegs    = 0;
    public int   $disabledRegs = 0;

    /** backing store keys */
    private const KEY_MODE        = 'receiver.mode';         // 'none'|'all'|'by_register'
    private const KEY_BLOCKED_IDS = 'receiver.blocked_ids';  // [ids]

    public function mount(): void
    {
        $this->isAdmin = ((int) auth()->user()->role) === 1;
        if (!$this->isAdmin) abort(403);

        $mode        = (string) (Setting::get(self::KEY_MODE, 'none'));
        $blockedIds  = array_map('intval', (array) Setting::get(self::KEY_BLOCKED_IDS, []));

        $this->disableAll = ($mode === 'all');

        $registers = User::query()
            ->where('role', 2)
            ->orderBy('name')
            ->get(['id','name']);

        $this->rows = $registers->map(fn ($u) => [
            'id'       => (int) $u->id,
            'name'     => $u->name,
            'disabled' => in_array((int) $u->id, $blockedIds, true),
        ])->values()->all();

        $this->recount();
    }

    private function recount(): void
    {
        $this->totalRegs    = count($this->rows);
        $this->disabledRegs = collect($this->rows)->where('disabled', true)->count();
    }

    /** Toggle one register row */
    public function toggleRegister(int $id): void
    {
        foreach ($this->rows as &$r) {
            if ($r['id'] === $id) {
                $r['disabled'] = !$r['disabled'];
                break;
            }
        }
        unset($r);
        $this->recount();
    }

    /** Bulk actions (optional buttons) */
    public function disableSelected(array $ids): void
    {
        $set = array_map('intval', $ids);
        foreach ($this->rows as &$r) if (in_array($r['id'], $set, true)) $r['disabled'] = true;
        unset($r);
        $this->recount();
    }
    public function enableAllRegisters(): void
    {
        foreach ($this->rows as &$r) $r['disabled'] = false;
        unset($r);
        $this->recount();
    }

    /** Persist */
    public function save(): void
    {
        if (!$this->isAdmin) abort(403);

        $mode = $this->disableAll ? 'all' : 'by_register';
        $blockedIds = $this->disableAll
            ? []  // irrelevant when global=all
            : collect($this->rows)
                ->where('disabled', true)
                ->pluck('id')
                ->map(fn($v) => (int) $v)
                ->unique()
                ->values()
                ->all();

        Setting::put(self::KEY_MODE, $mode);
        Setting::put(self::KEY_BLOCKED_IDS, $blockedIds);

        $this->dispatchBrowserEvent('alert', ['type'=>'success','message'=>__('Settings saved')]);
    }

    public function render()
    {
        return view('components.general.setting');
    }
}
