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
     * Returns correct validation errors when creating a property.
     *
     * @return void
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

        $this->assertDatabaseCount('properties', 0);
    }

    /**
     * Returns a successful response when creating a property.
     *
     * @return void
     */
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

        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseHas('properties', [
            'name' => 'My Property',
            'address' => 'My Address'
        ]);
    }

    /**
     * Returns successful response when the listing endpoint is called and there are no properties.
     *
     * @return void
     */
    public function test_returns_a_successful_response_when_the_listing_endpoint_is_called_and_there_are_no_properties(): void
    {
        $response = $this->getJson('/api/property');

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Properties retrieved",
                "data" => []
            ]);

        $this->assertDatabaseCount('properties', 0);
    }

    /**
     * Returns successful response when the listing endpoint is called and there are properties.
     *
     * @return void
     */
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

        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseCount('rooms', 1);
        $this->assertDatabaseHas('properties', [
            'name' => 'My Property',
            'address' => 'My Address'
        ]);
        $this->assertDatabaseHas('rooms', [
            'property_id' => $property->id,
            'name' => 'My Room',
            'size' => 100,
            'size_unit' => SizeUnit::SQUARE_METERS
        ]);
    }

    /**
     * Returns correct number of rooms object when the listing endpoint is called.
     *
     * @return void
     */
    public function test_returns_correct_number_of_rooms_object_when_the_listing_endpoint_is_called(): void
    {
        Property::factory()->hasRooms(17)->create();

        // Confirm that there are 17 rooms and 1 property in the database
        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseCount('rooms', 17);

        $response = $this->getJson('/api/property');
        $response->assertJson(fn (AssertableJson $json) =>
            $json->has('data.properties', 1)
                ->has('data.properties.0.rooms', 17)
                ->etc()
        );
    }

    /**
     * Update endpoint returns 404 when a property is not found.
     *
     * @return void
     */
    public function test_update_endpoint_returns_404_when_a_property_is_not_found(): void
    {
        $this->assertDatabaseCount('properties', 0);
        $response = $this->putJson('/api/property/1444', []);

        $response->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "message" => "Resource not found",
                "data" => []
            ]);
    }

    /**
     * Update endpoint returns 422 when validation fails.
     *
     * @return void
     */
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

        $dbProperty = Property::find($property->id);
        $this->assertEquals($dbProperty->name, $property->name);
        $this->assertEquals($dbProperty->address, $property->address);
    }

    /**
     * Can successfully update a property.
     *
     * @return void
     */
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

        $dbProperty = Property::find($property->id);
        $this->assertEquals($dbProperty->name, 'My Property');
        $this->assertEquals($dbProperty->address, 'My Address');
    }

    /**
     * Delete endpoint returns 404 when a property is not found.
     *
     * @return void
     */
    public function test_delete_endpoint_returns_404_when_a_property_is_not_found(): void
    {
        $this->assertDatabaseCount('properties', 0);
        $response = $this->deleteJson('/api/property/1444');

        $response->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "message" => "Resource not found",
                "data" => []
            ]);
    }

    /**
     * Can successfully delete a property.
     *
     * @return void
     */
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

        $this->assertDatabaseCount('properties', 0);
    }

    /**
     * Can successfully delete a property and its rooms.
     *
     * @return void
     */
    public function test_can_successfully_delete_a_property_and_its_rooms(): void
    {
        $property = Property::factory()->hasRooms(3)->create();
        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseCount('rooms', 3);

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
