<?php

namespace Tests\Unit;

use App\Services\SenderTransferQuoteService;
use PHPUnit\Framework\TestCase;

class SenderTransferQuoteServiceTest extends TestCase
{
    public function test_it_normalizes_mixed_bracket_shapes_and_picks_matching_fee(): void
    {
        $service = new SenderTransferQuoteService();

        $brackets = $service->normalizeBrackets([
            ['from' => 300, 'to' => null, 'tax' => 12],
            [0, 100, 5],
            ['min' => 101, 'max' => 299, 'fee' => 9],
        ]);

        $this->assertSame([
            [0.0, 100.0, 5.0],
            [101.0, 299.0, 9.0],
            [300.0, null, 12.0],
        ], $brackets);

        $this->assertSame(9.0, $service->commissionFromBrackets($brackets, 250));
        $this->assertSame(12.0, $service->commissionFromBrackets($brackets, 500));
    }
}
