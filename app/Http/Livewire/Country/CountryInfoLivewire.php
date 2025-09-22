<?php

namespace App\Http\Livewire\Country;

use Livewire\Component;
use App\Models\Country;
use App\Models\CountryLimit;
use App\Models\GeneralCountryLimit;
use App\Models\CountryTax;
use App\Models\GeneralCountryTax;
use App\Models\CountryRule;

class CountryInfoLivewire extends Component
{
    /** Normalize brackets of any shape into [[min,max,fee], ...] */
    protected function normalizeBrackets($brackets): array
    {
        // Decode JSON string if needed
        if (is_string($brackets)) {
            $decoded = json_decode($brackets, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $brackets = $decoded;
            } else {
                return [];
            }
        }
        if (!is_array($brackets)) return [];

        $out = [];
        foreach ($brackets as $row) {
            // Accept [min,max,fee]
            if (is_array($row) && array_keys($row) === [0,1,2]) {
                $min = (float) $row[0];
                $max = ($row[1] === null || $row[1] === '' || $row[1] === 0) ? null : (float) $row[1]; // open-ended
                $fee = (float) $row[2];
                $out[] = [$min, $max, $fee];
                continue;
            }
            // Accept associative {min,max,fee}
            if (is_array($row)) {
                $min = $row['min'] ?? $row['from'] ?? $row['start'] ?? null;
                $max = $row['max'] ?? $row['to']   ?? $row['end']   ?? null;
                $fee = $row['fee'] ?? $row['tax']  ?? $row['value'] ?? null;
                if ($min !== null && $fee !== null) {
                    $min = (float)$min;
                    $max = ($max === null || $max === '' || $max === 0) ? null : (float)$max;
                    $fee = (float)$fee;
                    $out[] = [$min, $max, $fee];
                }
            }
        }

        // Sort by min ascending
        usort($out, fn($a,$b) => $a[0] <=> $b[0]);

        return $out;
    }

    public function render()
    {
        // -------- Form Group 1: Limits --------
        $generalLimit = GeneralCountryLimit::latest('id')->first();
        $limitExceptions = CountryLimit::with('country')
            ->orderBy(
                Country::select('en_name')->whereColumn('countries.id', 'country_limits.country_id')
            )
            ->get();

        // -------- Form Group 2: Taxes --------
        $generalTax = GeneralCountryTax::latest('id')->first();
        $generalBrackets = $this->normalizeBrackets($generalTax->brackets_json ?? null);

        $assignments = CountryTax::with(['country', 'set'])
            ->orderBy(
                Country::select('en_name')->whereColumn('countries.id', 'country_taxes.country_id')
            )
            ->get();

        // Group by set name, attach each set's normalized brackets + its countries
        $setBlocks = $assignments
            ->groupBy(function ($a) {
                return optional($a->set)->name ?: 'Unspecified';
            })
            ->map(function ($rows, $setName) {
                $set = $rows->first()->set ?? null;
                $brackets = $set ? $this->normalizeBrackets($set->brackets_json ?? null) : [];
                $countries = $rows->pluck('country')->filter()->sortBy('en_name')->values()->all();
                return [
                    'name'      => $setName,
                    'brackets'  => $brackets,
                    'countries' => $countries,
                    'count'     => count($countries),
                ];
            })
            ->sortKeys()
            ->values()
            ->all();

        // -------- Form Group 3: Banned --------
        $banned = CountryRule::with('country')
            ->orderBy(
                Country::select('en_name')->whereColumn('countries.id', 'country_rules.country_id')
            )
            ->get();

        return view('components.forms.country-view', compact(
            'generalLimit',
            'limitExceptions',
            'generalTax',
            'generalBrackets',
            'setBlocks',
            'banned'
        ));
    }
}
