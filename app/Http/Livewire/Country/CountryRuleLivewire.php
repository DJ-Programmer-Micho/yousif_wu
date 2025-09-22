<?php

namespace App\Http\Livewire\Country;

use App\Models\Country;
use App\Models\CountryRule;
use Livewire\Component;
use Livewire\WithPagination;

class CountryRuleLivewire extends Component
{
    use WithPagination;

    public $country_id, $editId = null;
    public $showCreate = false, $showEdit = false;
    public $perPage = 10, $search = '';
    public $showDelete = false, $deleteId = null, $deleteName = '';

    // optional future hook: extra countries to hide from the create picker
    public array $excludeCountryIds = [];
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
        $usedIds = CountryRule::pluck('country_id')->all();

        $rules = CountryRule::with('country')
            ->when($this->search, fn($q) => $q->whereHas('country', fn($qq) =>
                $qq->where('en_name','like',"%{$this->search}%")
            ))
            ->orderBy(
                Country::select('en_name')->whereColumn('countries.id','country_rules.country_id')
            )
            ->paginate($this->perPage);

        $available = Country::query()
            ->whereNotIn('id', array_merge($usedIds, $this->excludeCountryIds))
            ->orderBy('en_name')
            ->get();

        return view('components.forms.country-rule-table', compact('rules','available'));
    }

    public function createOpen()
    {
        $this->reset(['country_id','editId']);
        $this->showCreate = true;
        $this->dispatchBrowserEvent('country-rule-create-opened', ['componentId' => $this->id]);
    }

    public function store()
    {
        $this->validate([
            'country_id' => ['required','exists:countries,id','unique:country_rules,country_id'],
        ]);

        CountryRule::create([
            'country_id' => $this->country_id,
            'rule'       => true, // always "not allowed"
        ]);

        $this->showCreate = false;
        session()->flash('success','Country added to Not-Allowed list.');
    }

    public function editOpen($id)
    {
        $row = CountryRule::findOrFail($id);
        $this->editId     = $row->id;
        $this->country_id = $row->country_id; // locked
        $this->showEdit   = true;
    }

    // no update fields (read-only). Keep method for symmetry if template calls it:
    public function update()
    {
        $this->showEdit = false; // no-op
    }

    public function confirmDelete($id): void
    {
        $row = CountryRule::with('country')->findOrFail($id);
        $this->deleteId   = $row->id;
        $this->deleteName = $row->country ? $row->country->en_name : 'Country';
        $this->showDelete = true;
    }

    public function delete(): void
    {
        $row = CountryRule::findOrFail($this->deleteId);
        $row->delete();

        $this->reset(['showDelete','deleteId','deleteName']);
        session()->flash('success', 'Country removed from Not-Allowed list.');
    }
}
