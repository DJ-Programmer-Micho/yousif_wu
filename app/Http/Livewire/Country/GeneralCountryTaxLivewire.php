<?php

namespace App\Http\Livewire\Country;

use App\Models\GeneralCountryTax;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class GeneralCountryTaxLivewire extends Component
{
    /** @var array<int, array{min:float|null, max:float|null, fee:float|null}> */
    public $brackets = [];

    // public function mount(): void
    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        
        if ((int) auth()->user()->role !== 1) {
            abort(403); // if you prefer a 403 instead of redirect
        }
        $g = GeneralCountryTax::find(1);

        if ($g && is_array($g->brackets_json) && count($g->brackets_json)) {
            // [[min,max,fee], ...] => [{min:..,max:..,fee:..}]
            $this->brackets = collect($g->brackets_json)
                ->map(fn($r) => ['min' => $r[0] ?? null, 'max' => $r[1] ?? null, 'fee' => $r[2] ?? null])
                ->values()->all();
        } else {
            $this->brackets = [['min' => null, 'max' => null, 'fee' => null]];
        }
    }

    public function render()
    {
        return view('components.forms.general-country-tax');
    }

    public function addBracketRow(): void
    {
        $this->brackets[] = ['min' => null, 'max' => null, 'fee' => null];
    }

    public function removeBracketRow($index): void
    {
        unset($this->brackets[$index]);
        $this->brackets = array_values($this->brackets); // reindex
    }

    /** @return array<int, array{0:float,1:float,2:float}> */
    protected function normalizeAndValidate(): array
    {
        $this->validate([
            'brackets'           => ['required','array','min:1'],
            'brackets.*.min'     => ['required','numeric'],
            'brackets.*.max'     => ['required','numeric'],
            'brackets.*.fee'     => ['required','numeric'],
        ], [], [
            'brackets.*.min' => 'Min',
            'brackets.*.max' => 'Max',
            'brackets.*.fee' => 'Fee',
        ]);

        // Normalize to numbers
        $rows = array_map(fn($r) => [
            'min' => (float)$r['min'],
            'max' => (float)$r['max'],
            'fee' => (float)$r['fee'],
        ], $this->brackets);

        // Sort ascending by min
        usort($rows, fn($a,$b) => $a['min'] <=> $b['min']);

        // Cross-row rules: min ≤ max, and non-overlapping ascending ranges
        $prevMax = null;
        foreach ($rows as $i => $r) {
            if ($r['min'] > $r['max']) {
                throw ValidationException::withMessages(["brackets.$i.min" => "Min must be ≤ Max."]);
            }
            if ($prevMax !== null && $r['min'] <= $prevMax) {
                throw ValidationException::withMessages(["brackets.$i.min" => "Ranges must be ascending and non-overlapping (this Min must be > previous Max)."]);
            }
            $prevMax = $r['max'];
        }

        // Convert back to DB shape: [[min,max,fee], ...]
        return array_map(fn($r) => [$r['min'], $r['max'], $r['fee']], $rows);
    }

    public function save(): void
    {
        $data = $this->normalizeAndValidate();

        $g = GeneralCountryTax::firstOrCreate(['id' => 1]);
        $g->update(['brackets_json' => $data]);

        session()->flash('success', 'General tax brackets saved.');
    }
}
