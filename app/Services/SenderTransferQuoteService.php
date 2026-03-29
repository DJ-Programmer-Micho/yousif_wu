<?php

namespace App\Services;

use App\Models\Country;
use App\Models\CountryLimit;
use App\Models\CountryRule;
use App\Models\CountryTax;
use App\Models\GeneralCountryLimit;
use App\Models\GeneralCountryTax;
use App\Models\SenderBalance;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Schema;

class SenderTransferQuoteService
{
    public function getBlockedCountryIds(): array
    {
        return CountryRule::pluck('country_id')->all();
    }

    public function getAvailableCountries(): array
    {
        $blockedIds = $this->getBlockedCountryIds();

        return Country::query()
            ->when($blockedIds, fn ($query) => $query->whereNotIn('id', $blockedIds))
            ->orderBy('en_name')
            ->get(['id', 'en_name', 'iso_code', 'ar_name', 'ku_name', 'flag_path'])
            ->toArray();
    }

    public function getCountry(?int $countryId): ?Country
    {
        if (!$countryId) {
            return null;
        }

        return Country::find($countryId);
    }

    public function getCountryIso(?int $countryId): ?string
    {
        return $this->getCountry($countryId)?->iso_code;
    }

    public function getLimits(?int $countryId): array
    {
        $min = null;
        $max = null;

        if ($countryId && ($countryLimit = CountryLimit::where('country_id', $countryId)->first())) {
            $min = $countryLimit->min_value !== null ? (float) $countryLimit->min_value : null;
            $max = $countryLimit->max_value !== null ? (float) $countryLimit->max_value : null;
        } elseif ($generalLimit = GeneralCountryLimit::latest('id')->first()) {
            $min = $generalLimit->min_value !== null ? (float) $generalLimit->min_value : null;
            $max = $generalLimit->max_value !== null ? (float) $generalLimit->max_value : null;
        }

        return ['min' => $min, 'max' => $max];
    }

    public function getRemainingBalanceForUser(?Authenticatable $user): ?float
    {
        if (!$user || (int) ($user->role ?? 0) !== 2) {
            return null;
        }

        if (!Schema::hasTable('sender_balances')) {
            return null;
        }

        $incoming = (float) SenderBalance::where('user_id', $user->id)
            ->where('status', 'Incoming')
            ->sum('amount');

        $outgoing = (float) SenderBalance::where('user_id', $user->id)
            ->whereIn('status', ['Outgoing', 'outcoming'])
            ->sum('amount');

        return round($incoming - $outgoing, 2);
    }

    public function quote(?int $countryId, mixed $amount): array
    {
        $limits = $this->getLimits($countryId);
        $normalizedAmount = is_numeric($amount) ? (float) $amount : null;

        if ($normalizedAmount === null) {
            return [
                'amount' => null,
                'commission' => 0.0,
                'total' => null,
                'receiver_gets' => null,
                'min' => $limits['min'],
                'max' => $limits['max'],
            ];
        }

        $commission = $this->calculateCommission($countryId, $normalizedAmount);

        return [
            'amount' => $normalizedAmount,
            'commission' => $commission,
            'total' => $normalizedAmount + $commission,
            'receiver_gets' => $normalizedAmount,
            'min' => $limits['min'],
            'max' => $limits['max'],
        ];
    }

    public function calculateCommission(?int $countryId, float $amount): float
    {
        $brackets = $this->normalizedBracketsForCountry($countryId);

        return $this->commissionFromBrackets($brackets, $amount);
    }

    public function normalizedBracketsForCountry(?int $countryId): array
    {
        $brackets = null;

        if ($countryId) {
            $assignment = CountryTax::with('set')->where('country_id', $countryId)->first();
            if ($assignment && $assignment->set) {
                $brackets = $assignment->set->brackets_json;
            }
        }

        if (!$brackets) {
            $generalTax = GeneralCountryTax::latest('id')->first();
            if ($generalTax) {
                $brackets = $generalTax->brackets_json;
            }
        }

        return $this->normalizeBrackets($brackets);
    }

    public function normalizeBrackets(mixed $brackets): array
    {
        if (is_string($brackets)) {
            $decoded = json_decode($brackets, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $brackets = $decoded;
            } else {
                return [];
            }
        }

        if (!is_array($brackets)) {
            return [];
        }

        $normalized = [];

        foreach ($brackets as $row) {
            if (is_array($row) && array_keys($row) === [0, 1, 2]) {
                $min = (float) $row[0];
                $max = $row[1] === null || $row[1] === '' ? null : (float) $row[1];
                $fee = (float) $row[2];

                $normalized[] = [$min, $max, $fee];
                continue;
            }

            if (!is_array($row)) {
                continue;
            }

            $min = $row['min'] ?? $row['from'] ?? $row['start'] ?? null;
            $max = $row['max'] ?? $row['to'] ?? $row['end'] ?? null;
            $fee = $row['fee'] ?? $row['tax'] ?? $row['value'] ?? null;

            if ($min === null || $fee === null) {
                continue;
            }

            $normalized[] = [
                (float) $min,
                ($max === null || $max === '' || $max === 0) ? null : (float) $max,
                (float) $fee,
            ];
        }

        usort($normalized, fn ($left, $right) => $left[0] <=> $right[0]);

        return $normalized;
    }

    public function commissionFromBrackets(array $brackets, float $amount): float
    {
        foreach ($brackets as $row) {
            if (!is_array($row) || count($row) !== 3) {
                continue;
            }

            [$min, $max, $fee] = $row;
            $min = (float) $min;
            $max = $max === null ? null : (float) $max;

            if ($amount >= $min && ($max === null || $amount <= $max)) {
                return (float) $fee;
            }
        }

        return 0.0;
    }
}
