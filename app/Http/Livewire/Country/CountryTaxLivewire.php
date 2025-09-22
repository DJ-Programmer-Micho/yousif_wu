<?php

namespace App\Http\Livewire\Country;

use App\Models\Country;
use Livewire\Component;
use App\Models\CountryTax;
use App\Models\CountryRule;
use App\Models\TaxBracketSet;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CountryTaxLivewire extends Component
{
    // ---- Bracket sets state ----
    public $set_name;
    public $brackets = []; // rows: [['min'=>, 'max'=>, 'fee'=>], ...]
    public $editSetId = null;

    // ---- Assignments state ----
    public $country_id, $tax_bracket_set_id, $editAssignId = null;

    // ---- Delete modals ----
    public $showDeleteSetModal = false, $deleteSetId = null, $deleteSetName = '';
    public $showDeleteAssignModal = false, $deleteAssignId = null, $deleteAssignSummary = '';

    // ---- Modals ----
    public $showSetModal = false, $showSetEditModal = false, $showAssignModal = false, $showAssignEditModal = false;


    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        
        if ((int) auth()->user()->role !== 1) {
            abort(403); // if you prefer a 403 instead of redirect
        }
    }
    public function render()
    {
        $sets = TaxBracketSet::orderBy('name')->get();

        $assignedCountryIds = CountryTax::pluck('country_id')->all();
        $blocked            = CountryRule::pluck('country_id')->all(); // NEW

        $availableCountries = Country::whereNotIn('id', array_unique(array_merge($assignedCountryIds, $blocked)))
            ->orderBy('en_name')
            ->get();

        $assignments = CountryTax::with(['country','set'])->orderBy(
            Country::select('en_name')->whereColumn('countries.id','country_taxes.country_id')
        )->get();

        $setUsageCounts = CountryTax::selectRaw('tax_bracket_set_id, COUNT(*) as c')
            ->groupBy('tax_bracket_set_id')
            ->pluck('c','tax_bracket_set_id')
            ->toArray();

        return view('components.forms.country-tax-table', compact('sets','assignments','availableCountries','setUsageCounts'));
    }


    /* ===================== Bracket Sets (UI rows) ===================== */

    public function openCreateSet(): void
    {
        $this->reset(['set_name','brackets','editSetId']);
        $this->brackets = [['min'=>null,'max'=>null,'fee'=>null]];
        $this->showSetModal = true;
    }

    public function openEditSet($id): void
    {
        $row = TaxBracketSet::findOrFail($id);
        $this->editSetId = $row->id;
        $this->set_name  = $row->name;
        $this->brackets = collect($row->brackets_json ?? [])
            ->map(fn($r) => ['min'=>$r[0] ?? null, 'max'=>$r[1] ?? null, 'fee'=>$r[2] ?? null])
            ->values()->all();
        if (empty($this->brackets)) $this->brackets = [['min'=>null,'max'=>null,'fee'=>null]];
        $this->showSetEditModal = true;
    }

    public function addBracketRow(): void { $this->brackets[] = ['min'=>null,'max'=>null,'fee'=>null]; }

    public function removeBracketRow($index): void
    {
        unset($this->brackets[$index]);
        $this->brackets = array_values($this->brackets);
    }

    protected function normalizeAndValidateBrackets(): array
    {
        $this->validate([
            'set_name'           => ['required','string','max:255'],
            'brackets'           => ['required','array','min:1'],
            'brackets.*.min'     => ['required','numeric'],
            'brackets.*.max'     => ['required','numeric'],
            'brackets.*.fee'     => ['required','numeric'],
        ], [], [
            'brackets.*.min' => 'Min',
            'brackets.*.max' => 'Max',
            'brackets.*.fee' => 'Fee',
        ]);

        $rows = array_map(fn($r) => [
            'min' => (float)$r['min'],
            'max' => (float)$r['max'],
            'fee' => (float)$r['fee'],
        ], $this->brackets);

        usort($rows, fn($a,$b) => $a['min'] <=> $b['min']);

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

        return array_map(fn($r) => [$r['min'],$r['max'],$r['fee']], $rows);
    }

    public function storeSet(): void
    {
        $data = $this->normalizeAndValidateBrackets();
        TaxBracketSet::create(['name' => $this->set_name, 'brackets_json' => $data]);
        $this->showSetModal = false;
        session()->flash('success', 'Tax set created.');
    }

    public function updateSet(): void
    {
        $row  = TaxBracketSet::findOrFail($this->editSetId);
        $data = $this->normalizeAndValidateBrackets();
        $row->update(['name' => $this->set_name, 'brackets_json' => $data]);
        $this->showSetEditModal = false;
        session()->flash('success', 'Tax set updated.');
    }

    /* ==================== Delete Set ==================== */

    public function confirmDeleteSet($id): void
    {
        $set = TaxBracketSet::findOrFail($id);
        // block if in use
        if (CountryTax::where('tax_bracket_set_id', $id)->exists()) {
            session()->flash('error', 'This set is assigned to one or more countries. Unassign it first.');
            return;
        }
        $this->deleteSetId = $id;
        $this->deleteSetName = $set->name;
        $this->showDeleteSetModal = true;
    }

    public function deleteSet(): void
    {
        $set = TaxBracketSet::findOrFail($this->deleteSetId);
        if (CountryTax::where('tax_bracket_set_id', $set->id)->exists()) {
            $this->showDeleteSetModal = false;
            session()->flash('error', 'Cannot delete: set is assigned. Unassign it first.');
            return;
        }
        $set->delete();
        $this->reset(['deleteSetId','deleteSetName','showDeleteSetModal']);
        session()->flash('success', 'Set deleted.');
    }

    /* ==================== Assignments (country -> set) ==================== */

    public function openCreateAssign(): void
    {
        $this->reset(['country_id','tax_bracket_set_id','editAssignId']);
        $this->showAssignModal = true;
        $this->dispatchBrowserEvent('country-tax-assign-opened', ['componentId' => $this->id]);
    }

    public function storeAssign(): void
    {
        $blocked = CountryRule::pluck('country_id')->all(); // NEW

        $this->validate([
            'country_id'         => [
                'required','exists:countries,id','unique:country_taxes,country_id',
                Rule::notIn($blocked), // NEW: cannot pick blocked country
            ],
            'tax_bracket_set_id' => ['required','exists:tax_bracket_sets,id'],
        ]);

        CountryTax::create([
            'country_id'         => $this->country_id,
            'tax_bracket_set_id' => $this->tax_bracket_set_id
        ]);

        $this->showAssignModal = false;
        session()->flash('success', 'Assigned tax set to country.');
    }

    public function openEditAssign($id): void
    {
        $row = CountryTax::findOrFail($id);
        $this->editAssignId       = $row->id;
        $this->country_id         = $row->country_id; // fixed in UI
        $this->tax_bracket_set_id = $row->tax_bracket_set_id;
        $this->showAssignEditModal = true;
        $this->dispatchBrowserEvent('country-tax-assign-edit-opened', ['componentId' => $this->id]);
    }

    public function updateAssign(): void
    {
        $row = CountryTax::findOrFail($this->editAssignId);
        $this->validate(['tax_bracket_set_id' => ['required','exists:tax_bracket_sets,id']]);
        $row->update(['tax_bracket_set_id' => $this->tax_bracket_set_id]);
        $this->showAssignEditModal = false;
        session()->flash('success', 'Assignment updated.');
    }

    /* ==================== Delete Assignment ==================== */

    public function confirmDeleteAssign($id): void
    {
        $row = CountryTax::with(['country','set'])->findOrFail($id);
        $this->deleteAssignId = $row->id;
        $this->deleteAssignSummary = ($row->country->en_name ?? 'Country') . ' → ' . ($row->set->name ?? 'Set');
        $this->showDeleteAssignModal = true;
    }

    public function deleteAssign(): void
    {
        $row = CountryTax::findOrFail($this->deleteAssignId);
        $row->delete();
        $this->reset(['deleteAssignId','deleteAssignSummary','showDeleteAssignModal']);
        session()->flash('success', 'Assignment deleted.');
    }
}
