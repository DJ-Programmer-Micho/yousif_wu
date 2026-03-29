<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateFeedService
{
    public const SOURCE_URL = 'https://qamaralfajr.com/production/exchange_rates.php';
    public const SOURCE_BASE_URL = 'https://qamaralfajr.com/production/';
    protected const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0 Safari/537.36';

    protected const FRESH_CACHE_KEY = 'dashboard.exchange_rates.current';
    protected const LAST_SUCCESS_CACHE_KEY = 'dashboard.exchange_rates.last_success';

    protected array $currencyNames = [
        'IQD' => 'Iraqi Dinar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'TRY' => 'Turkish Lira',
        'IRR' => 'Iranian Rial',
        'NOK' => 'Norwegian Krone',
        'SEK' => 'Swedish Krona',
        'JOD' => 'Jordanian Dinar',
        'SAR' => 'Saudi Riyal',
        'AED' => 'UAE Dirham',
        'CAD' => 'Canadian Dollar',
        'AUD' => 'Australian Dollar',
        'CHF' => 'Swiss Franc',
        'DKK' => 'Danish Krone',
        'QAR' => 'Qatari Riyal',
        'KWD' => 'Kuwaiti Dinar',
    ];

    public function getFeed(bool $forceRefresh = false): array
    {
        $cached = $forceRefresh ? null : Cache::get(self::FRESH_CACHE_KEY);
        if (is_array($cached) && (!empty($cached['rates']) || !empty($cached['table_html']))) {
            return $cached;
        }

        try {
            $feed = $this->fetchFeed();
            Cache::put(self::FRESH_CACHE_KEY, $feed, now()->addMinutes(10));
            Cache::forever(self::LAST_SUCCESS_CACHE_KEY, $feed);

            return $feed;
        } catch (\Throwable $exception) {
            $stale = Cache::get(self::LAST_SUCCESS_CACHE_KEY);
            if (is_array($stale) && !empty($stale['rates'])) {
                $stale['is_stale'] = true;
                $stale['error'] = __('Showing the latest cached exchange rates right now.');

                return $stale;
            }

            return [
                'updated_at' => null,
                'fetched_at' => now()->toIso8601String(),
                'rates' => [],
                'source_url' => self::SOURCE_URL,
                'is_stale' => false,
                'error' => __('Exchange rates are temporarily unavailable.'),
            ];
        }
    }

    public function parseHtml(string $html): array
    {
        $updatedAt = null;
        if (preg_match('/\b(?<date>\d{4}-\d{2}-\d{2})\b/', $html, $matches)) {
            $updatedAt = $matches['date'];
        }

        $prepared = preg_replace('/<img[^>]*>/iu', ' ', $html);
        $prepared = preg_replace('/<(br|\/p|\/div|\/li|\/tr|\/table|\/section|\/article|\/h[1-6])[^>]*>/iu', "\n", $prepared);
        $prepared = html_entity_decode(strip_tags((string) $prepared), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $prepared = preg_replace("/\r\n|\r/u", "\n", $prepared);

        $lines = array_values(array_filter(array_map(function (string $line): string {
            $line = preg_replace('/\s+/u', ' ', trim($line));

            return (string) $line;
        }, explode("\n", $prepared))));

        $rates = [];
        foreach ($lines as $line) {
            if (!preg_match('/^(?<sell>\d+(?:\.\d+)?)\s+(?<buy>\d+(?:\.\d+)?)\s+(?<label>.+?)\s+(?<code>[A-Za-z]{3})$/u', $line, $matches)) {
                continue;
            }

            $code = strtoupper($matches['code']);
            if (isset($rates[$code])) {
                continue;
            }

            $rates[$code] = [
                'code' => $code,
                'name' => $this->currencyNames[$code] ?? $code,
                'label' => trim($matches['label']),
                'sell' => (float) $matches['sell'],
                'buy' => (float) $matches['buy'],
            ];
        }

        return [
            'updated_at' => $updatedAt,
            'fetched_at' => now()->toIso8601String(),
            'rates' => array_values($rates),
            'table_html' => $this->extractTableHtml($html),
            'source_url' => self::SOURCE_URL,
            'is_stale' => false,
            'error' => null,
        ];
    }

    public function extractTableHtml(string $html): ?string
    {
        if (!preg_match('/<table\b[^>]*>.*?<\/table>/isu', $html, $matches)) {
            return null;
        }

        $table = (string) $matches[0];
        $table = preg_replace('/<script\b[^>]*>.*?<\/script>/isu', '', $table);
        $table = preg_replace('/<style\b[^>]*>.*?<\/style>/isu', '', $table);
        $table = preg_replace('/\son[a-z0-9_-]+\s*=\s*(["\']).*?\1/isu', '', $table);
        $table = preg_replace_callback(
            '/(<img\b[^>]*\bsrc=["\'])([^"\']+)(["\'][^>]*>)/isu',
            function (array $matches): string {
                return $matches[1] . $this->normalizeAssetUrl($matches[2]) . $matches[3];
            },
            $table
        );

        return trim((string) $table) ?: null;
    }

    protected function fetchFeed(): array
    {
        $html = Http::timeout(12)
            ->retry(2, 250)
            ->accept('text/html')
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept-Language' => 'en-US,en;q=0.9,ar;q=0.8',
                'Referer' => self::SOURCE_URL,
            ])
            ->get(self::SOURCE_URL)
            ->throw()
            ->body();

        return $this->parseHtml($html);
    }

    protected function normalizeAssetUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '' || preg_match('/^(?:https?:)?\/\//iu', $url)) {
            return $url;
        }

        if (str_starts_with($url, '/')) {
            return 'https://qamaralfajr.com' . $url;
        }

        return self::SOURCE_BASE_URL . ltrim($url, '/');
    }
}
