<?php

namespace App\Http\Livewire\Country;

use App\Models\Country;
use Livewire\Component;
use App\Models\CountryRule;
use App\Models\CountryLimit;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class CountryLimitLivewire extends Component
{
    use WithPagination;

    public $country_id, $min_value, $max_value;
    public $editId = null;
    public $showCreate = false;
    public $showEdit = false;
    public $perPage = 10;
    public $search = '';
    public $showDeleteLimitModal = false, $deleteLimitId = null, $deleteLimitSummary = '';

    protected $listeners = ['refreshLimits' => '$refresh'];
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
        $used = CountryLimit::pluck('country_id')->all();
        $blocked = CountryRule::pluck('country_id')->all();

        $limits = CountryLimit::query()
            ->with('country')
            ->when($this->search, fn($q) => $q->whereHas('country', fn($qq) =>
                $qq->where('en_name', 'like', "%{$this->search}%")
            ))
            ->orderBy(Country::select('en_name')->whereColumn('countries.id','country_limits.country_id'))
            ->paginate($this->perPage);

        // For create: exclude already-used countries
        $availableCountries = Country::whereNotIn('id', array_unique(array_merge($used, $blocked)))
        ->orderBy('en_name')
        ->get();

        return view('components.forms.country-limit-table', compact('limits','availableCountries'));
    }

    public function createOpen()
    {
        $this->reset(['country_id','min_value','max_value','editId']);
        $this->showCreate = true;
        $this->dispatchBrowserEvent('country-limit-create-opened', [
            'componentId' => $this->id, // <— pass Livewire component id
        ]);
    }

    public function store()
    {
        $blocked = CountryRule::pluck('country_id')->all();
        $this->validate([
            'country_id' => [
                'required','exists:countries,id','unique:country_limits,country_id',
                Rule::notIn($blocked), // NEW: cannot pick blocked country
            ],
            'min_value'  => ['required','numeric','min:0'],
            'max_value'  => ['required','numeric','gt:min_value'],
        ]);

        CountryLimit::create([
            'country_id' => $this->country_id,
            'min_value'  => $this->min_value,
            'max_value'  => $this->max_value,
        ]);

        $this->showCreate = false;
        $this->emit('refreshLimits');
        session()->flash('success','Limit created.');
    }

    public function editOpen($id)
    {
        $row = CountryLimit::findOrFail($id);
        $this->editId     = $row->id;
        $this->country_id = $row->country_id; // fixed (cannot change in UI)
        $this->min_value  = $row->min_value;
        $this->max_value  = $row->max_value;
        $this->showEdit = true;
    }

    public function update()
    {
        $row = CountryLimit::findOrFail($this->editId);

        $this->validate([
            'min_value' => ['required','numeric','min:0'],
            'max_value' => ['required','numeric','gt:min_value'],
        ]);

        $row->update([
            'min_value' => $this->min_value,
            'max_value' => $this->max_value,
        ]);

        $this->showEdit = false;
        $this->emit('refreshLimits');
        session()->flash('success','Limit updated.');
    }

    public function confirmDeleteLimit($id): void
    {
        $row = CountryLimit::with('country')->findOrFail($id);
        $this->deleteLimitId = $row->id;
        $this->deleteLimitSummary = $row->country->en_name ?? 'Country';
        $this->showDeleteLimitModal = true;
    }

    public function deleteLimit(): void
    {
        $row = CountryLimit::findOrFail($this->deleteLimitId);
        $row->delete();

        // Reset modal state
        $this->reset(['showDeleteLimitModal','deleteLimitId','deleteLimitSummary']);

        // If current page became empty, go back one page for good UX
        if ($this->page > 1 && CountryLimit::count() <= ($this->page - 1) * $this->perPage) {
            $this->previousPage();
        }

        $this->emit('refreshLimits');
        session()->flash('success', 'Limit deleted.');
    }
}
