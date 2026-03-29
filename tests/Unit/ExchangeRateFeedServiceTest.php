<?php

namespace Tests\Unit;

use App\Services\ExchangeRateFeedService;
use PHPUnit\Framework\TestCase;

class ExchangeRateFeedServiceTest extends TestCase
{
    public function test_it_parses_rates_from_html(): void
    {
        $service = new ExchangeRateFeedService();

        $html = <<<'HTML'
            <div>2026-03-21</div>
            <div>SELL BUY CURRENCY</div>
            <div>155000 153000 دینار IQD</div>
            <div>116 114.5 یۆرۆ EUR</div>
            <div>375 395 قەتەری QAR</div>
        HTML;

        $feed = $service->parseHtml($html);

        $this->assertSame('2026-03-21', $feed['updated_at']);
        $this->assertCount(3, $feed['rates']);
        $this->assertSame('IQD', $feed['rates'][0]['code']);
        $this->assertSame(153000.0, $feed['rates'][0]['buy']);
        $this->assertSame('QAR', $feed['rates'][2]['code']);
        $this->assertSame('Qatari Riyal', $feed['rates'][2]['name']);
        $this->assertNull($feed['table_html']);
    }

    public function test_it_extracts_the_remote_exchange_table_markup(): void
    {
        $service = new ExchangeRateFeedService();

        $html = <<<'HTML'
            <div>2026-03-21</div>
            <table style="border:none;width:100%;">
              <tbody>
                <tr>
                  <td><button class="btn btn-primary">154200</button></td>
                  <td><button class="btn btn-primary">154000</button></td>
                  <td><img src="upload/currencies/test.png" width="70px"></td>
                  <td>دینار IQD</td>
                </tr>
              </tbody>
            </table>
        HTML;

        $table = $service->extractTableHtml($html);

        $this->assertNotNull($table);
        $this->assertStringContainsString('<table', $table);
        $this->assertStringContainsString('https://qamaralfajr.com/production/upload/currencies/test.png', $table);
    }
}
