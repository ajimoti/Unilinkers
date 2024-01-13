<?php

namespace Database\Factories;

use App\Enums\SizeUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Property;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'name' => $this->faker->name,
            'size' => $this->faker->numberBetween(1, 100),
            'size_unit' => $this->faker->randomElement(SizeUnit::values())
        ];
    }
}
