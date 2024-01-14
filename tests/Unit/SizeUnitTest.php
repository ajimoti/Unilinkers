<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Enums\SizeUnit;

class SizeUnitTest extends TestCase
{
    /**
     * Returns the correct size unit.
     *
     * @return void
     */
    public function test_returns_the_correct_size_unit(): void
    {
        $this->assertEquals('sqm', SizeUnit::SQUARE_METERS->value);
        $this->assertEquals('sqft', SizeUnit::SQUARE_FEET->value);
    }

    /**
     * Returns the correct size unit values.
     *
     * @return void
     */
    public function test_returns_the_correct_size_unit_values(): void
    {
        $this->assertEquals(['sqft', 'sqm'], SizeUnit::values());
    }
}
