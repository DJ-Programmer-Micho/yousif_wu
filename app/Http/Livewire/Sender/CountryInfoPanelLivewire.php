<?php

namespace App\Http\Livewire\Sender;

use Livewire\Component;
use App\Models\Country;
use App\Models\CountryLimit;
use App\Models\GeneralCountryLimit;
use App\Models\CountryTax;
use App\Models\GeneralCountryTax;

class CountryInfoPanelLivewire extends Component
{
    public $countryId = null;

    public ?array $limits = null;
    public ?array $brackets = null;
    public ?Country $country = null;

    protected $listeners = ['countryChanged' => 'setCountry'];

    public function mount($countryId = null): void
    {
        $this->countryId = $countryId;
        $this->hydrateData();
    }

    public function setCountry($id): void
    {
        $this->countryId = $id ?: null;
        $this->hydrateData();
    }

    protected function hydrateData(): void
    {
        $this->country = $this->countryId ? Country::find($this->countryId) : null;

        // Limits: country → general
        $min = $max = null;
        if ($this->countryId && ($cl = CountryLimit::where('country_id', $this->countryId)->first())) {
            $min = $cl->min_value !== null ? (float)$cl->min_value : null;
            $max = $cl->max_value !== null ? (float)$cl->max_value : null;
        } elseif ($g = GeneralCountryLimit::latest('id')->first()) {
            $min = $g->min_value !== null ? (float)$g->min_value : null;
            $max = $g->max_value !== null ? (float)$g->max_value : null;
        }
        $this->limits = ['min' => $min, 'max' => $max];

        // Brackets: country assignment → general
        $brackets = null;
        if ($this->countryId) {
            $assign = CountryTax::with('set')->where('country_id', $this->countryId)->first();
            if ($assign && $assign->set && is_array($assign->set->brackets_json)) {
                $brackets = $assign->set->brackets_json;
            }
        }
        if (!$brackets) {
            $g = GeneralCountryTax::find(1);
            if ($g && is_array($g->brackets_json)) $brackets = $g->brackets_json;
        }
        $this->brackets = is_array($brackets)
            ? array_values(array_filter($brackets, fn($r) => is_array($r) && count($r) === 3))
            : null;
    }

    public function render()
    {
        return view('components.forms.sender-view');
    }
}
