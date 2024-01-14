<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Room;

class RoomTest extends TestCase
{
    /**
     * Returns the correct human readable size.
     *
     * @return void
     */
    public function test_returns_correct_human_readable_size(): void
    {
        $this->assertEquals('100 sqm', Room::factory()->create([
            'size' => 100,
            'size_unit' => 'sqm'
        ])->human_readable_size);

        $this->assertEquals('100 sqft', Room::factory()->create([
            'size' => 100,
            'size_unit' => 'sqft'
        ])->human_readable_size);
    }
}
