<?php

namespace Tests\Feature;

use App\Models\Property;
use Tests\TestCase;
use App\Enums\SizeUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_returns_correct_validation_errors_when_creating_a_property(): void
    {
        $response = $this->postJson('/api/property', []);

        $response->assertStatus(422)
            ->assertJson([
                "status" => "error",
                "message" => "The given data was invalid",
                "data" => [
                    "name" => [
                        "The name field is required."
                    ],
                    "address" => [
                        "The address field is required."
                    ]
                ]
            ]);
    }

    public function test_returns_a_successful_response_when_creating_a_property(): void
    {
        $response = $this->postJson('/api/property', [
            'name' => 'My Property',
            'address' => 'My Address'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                "status" => "success",
                "message" => "Property created",
                "data" => [
                    "name" => "My Property",
                    "address" => "My Address"
                ]
            ]);
    }

    public function test_returns_a_successful_response_when_the_listing_endpoint_is_called_and_there_are_no_properties(): void
    {
        $response = $this->getJson('/api/property');

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Properties retrieved",
                "data" => []
            ]);
    }

    public function test_returns_a_successful_response_when_the_listing_endpoint_is_called_and_there_are_properties(): void
    {
        $property = Property::factory()->hasRooms(1, [
            'name' => 'My Room',
            'size' => '100',
            'size_unit' => SizeUnit::SQUARE_METERS
        ])->create([
            'name' => 'My Property',
            'address' => 'My Address'
        ]);

        $response = $this->getJson('/api/property');
        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Properties retrieved",
                "data" => [
                    "properties" => [
                        [
                            "id" => $property->id,
                            "name" => "My Property",
                            "address" => "My Address",
                            "rooms" => [
                                [
                                    "id" => 1,
                                    "property_id" => $property->id,
                                    "name" => "My Room",
                                    "size" => 100,
                                    "size_unit" => "sqm",
                                    "human_readable_size" => "100 sqm",
                                ]
                            ]
                        ]
                    ],
                    "pagination" => [
                        "total" => 1,
                        "count" => 1,
                        "per_page" => 10,
                        "current_page" => 1,
                        "total_pages" => 1,
                        "links" => [
                            "first" => config('app.url') . "/api/property?page=1",
                            "last" => config('app.url') . "/api/property?page=1",
                            "previous" => null,
                            "next" => null
                        ]
                    ]
                ]
            ]);
    }

    public function test_returns_correct_number_of_rooms_object_when_the_listing_endpoint_is_called(): void
    {
        Property::factory()->hasRooms(17)->create();

        $response = $this->getJson('/api/property');
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.properties', 1)
                ->has('data.properties.0.rooms', 17)
                ->etc()
        );
    }

    public function test_update_endpoint_returns_404_when_a_property_is_not_found(): void
    {
        $response = $this->putJson('/api/property/1444', []);

        $response->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "message" => "Resource not found",
                "data" => []
            ]);
    }

    public function test_update_endpoint_returns_422_when_validation_fails(): void
    {
        $property = Property::factory()->create();

        $response = $this->putJson("/api/property/{$property->id}", []);

        $response->assertStatus(422)
            ->assertJson([
                "status" => "error",
                "message" => "The given data was invalid",
                "data" => [
                    "name" => [
                        "The name field is required."
                    ],
                    "address" => [
                        "The address field is required."
                    ]
                ]
            ]);
    }

    public function test_can_successfully_update_a_property(): void
    {
        $property = Property::factory()->create();

        $response = $this->putJson("/api/property/{$property->id}", [
            'name' => 'My Property',
            'address' => 'My Address'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Property updated",
                "data" => [
                    "name" => "My Property",
                    "address" => "My Address"
                ]
            ]);
    }

    public function test_delete_endpoint_returns_404_when_a_property_is_not_found(): void
    {
        $response = $this->deleteJson('/api/property/1444');

        $response->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "message" => "Resource not found",
                "data" => []
            ]);
    }

    public function test_can_successfully_delete_a_property(): void
    {
        $property = Property::factory()->create();

        $response = $this->deleteJson("/api/property/{$property->id}");

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Property deleted",
                "data" => []
            ]);
    }

    public function test_can_successfully_delete_a_property_and_its_rooms(): void
    {
        $property = Property::factory()->hasRooms(1)->create();

        $response = $this->deleteJson("/api/property/{$property->id}");

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Property deleted",
                "data" => []
            ]);

        $this->assertDatabaseMissing('properties', [
            'id' => $property->id
        ]);

        $this->assertDatabaseMissing('rooms', [
            'property_id' => $property->id
        ]);
    }
}
