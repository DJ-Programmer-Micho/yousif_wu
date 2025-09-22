<?php

namespace App\Http\Livewire\Country;

use App\Models\GeneralCountryLimit;
use Livewire\Component;

class GeneralCountryLimitLivewire extends Component
{
    public $min_value, $max_value;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        
        if ((int) auth()->user()->role !== 1) {
            abort(403); // if you prefer a 403 instead of redirect
        }
        $g = GeneralCountryLimit::find(1);
        $this->min_value = $g->min_value ?? 0;
        $this->max_value = $g->max_value ?? 0;
    }

    public function render(){ return view('components.forms.general-country-limit'); }

    public function save()
    {
        $this->validate([
            'min_value' => ['required','numeric','min:0'],
            'max_value' => ['required','numeric','gt:min_value'],
        ]);
        $g = GeneralCountryLimit::firstOrCreate(['id'=>1]);
        $g->update(['min_value'=>$this->min_value,'max_value'=>$this->max_value]);
        session()->flash('success','General limits saved.');
    }
}
