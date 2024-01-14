<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Testing\Fluent\AssertableJson;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Returns correct validation errors when creating a room.
     *
     * @return void
     */
    public function test_returns_correct_validation_errors_when_creating_a_room(): void
    {
        $response = $this->postJson('/api/room', []);

        $response->assertStatus(422)
            ->assertJson([
                "status" => "error",
                "message" => "The given data was invalid",
                "data" => [
                    "property_id" => [
                        "The property id field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                    "size" => [
                        "The size field is required."
                    ],
                    "size_unit" => [
                        "The size unit field is required."
                    ]
                ]
            ]);

        $this->assertDatabaseCount('rooms', 0);
    }

    /**
     * Returns a successful response when creating a room.
     *
     * @return void
     */
    public function test_returns_a_successful_response_when_creating_a_room(): void
    {
        $property = Property::factory()->create();

        $response = $this->postJson('/api/room', [
            'property_id' => $property->id,
            'name' => 'My Room',
            'size' => 100,
            'size_unit' => 'sqm'
        ]);

        $response->assertStatus(201)
            ->assertJson([
                "status" => "success",
                "message" => "Room created",
                "data" => [
                    "property_id" => $property->id,
                    "name" => "My Room",
                    "size" => 100,
                    "size_unit" => "sqm"
                ]
            ]);

        $this->assertDatabaseCount('rooms', 1);
    }

    /**
     * Returns all rooms for a property.
     *
     * @return void
     */
    public function test_returns_all_rooms_for_a_property(): void
    {
        $property = Property::factory()->hasRooms(1, [
            'name' => 'My Room',
            'size' => 100,
            'size_unit' => 'sqm'
        ])->create();

        $response = $this->getJson("/api/room/{$property->id}");

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Rooms retrieved",
                "data" => [
                    [
                        "property_id" => $property->id,
                        "name" => "My Room",
                        "size" => 100,
                        "size_unit" => "sqm"
                    ]
                ]
            ]);
    }

    /*  Returns correct number of rooms for a property.
     *
     * @return void
     */
    public function test_returns_correct_number_of_rooms_for_a_property(): void
    {
        $property = Property::factory()->hasRooms(20)->create();

        $response = $this->getJson("/api/room/{$property->id}");

        $response->assertJson(fn (AssertableJson $json) =>
                $json->has('data', 20)
                    ->etc()
            );
    }

    /**
     * Successfully updates a room.
     *
     * @return void
     */
    public function test_successfully_updates_a_room(): void
    {
        $property = Property::factory()->hasRooms(1, [
            'name' => 'My Room',
            'size' => 100,
            'size_unit' => 'sqm'
        ])->create();

        // Create a second property to test that the room  property_id can be updated
        $property2 = Property::factory()->create();

        $roomId = $property->rooms->first()->id;
        $response = $this->putJson("/api/room/{$roomId}", [
            'property_id' => $property2->id,
            'name' => 'My Updated Room',
            'size' => 200,
            'size_unit' => 'sqm'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Room updated",
                "data" => [
                    "id" => $roomId,
                    "property_id" => $property2->id,
                    "name" => "My Updated Room",
                    "size" => 200,
                    "size_unit" => "sqm",
                    "human_readable_size"=> "200 sqm"
                ]
            ]);

        $updatedRoom = Room::find($roomId);
        $this->assertEquals($property2->id, $updatedRoom->property_id);
        $this->assertEquals('My Updated Room', $updatedRoom->name);
        $this->assertEquals(200, $updatedRoom->size);
        $this->assertEquals('sqm', $updatedRoom->size_unit);
    }

    /**
     * Returns a 404 error when updating a room that does not exist.
     *
     * @return void
     */
    public function test_returns_a_404_error_when_updating_a_room_that_does_not_exist(): void
    {
        $this->assertDatabaseCount('rooms', 0);
        $response = $this->putJson("/api/room/122", [
            'property_id' => 1,
            'name' => 'My Updated Room',
            'size' => 200,
            'size_unit' => 'sqm'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "message" => "Resource not found",
                "data" => []
            ]);
    }

    /**
     * Returns validation errors on update endpoint.
     *
     * @return void
     */
    public function test_validation_errors_on_update_endpoint(): void
    {
        $property = Property::factory()->hasRooms(1, [
            'name' => 'My Room',
            'size' => 100,
            'size_unit' => 'sqm'
        ])->create();

        $roomId = $property->rooms->first()->id;
        $response = $this->putJson("/api/room/{$roomId}", []);

        $response->assertStatus(422)
            ->assertJson([
                "status" => "error",
                "message" => "The given data was invalid",
                "data" => [
                    "property_id" => [
                        "The property id field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                    "size" => [
                        "The size field is required."
                    ],
                    "size_unit" => [
                        "The size unit field is required."
                    ]
                ]
            ]);
    }

    /**
     * Returns a 404 error when deleting a room that does not exist.
     *
     * @return void
     */
    public function test_returns_a_404_error_when_deleting_a_room_that_does_not_exist(): void
    {
        $this->assertDatabaseCount('rooms', 0);
        $response = $this->deleteJson("/api/room/122");

        $response->assertStatus(404)
            ->assertJson([
                "status" => "error",
                "message" => "Resource not found",
                "data" => []
            ]);
    }

    /**
     * Successfully deletes a room.
     *
     * @return void
     */
    public function test_successfully_deletes_a_room(): void
    {
        $property = Property::factory()->hasRooms(1, [
            'name' => 'My Room',
            'size' => 100,
            'size_unit' => 'sqm'
        ])->create();

        $roomId = $property->rooms->first()->id;
        $response = $this->deleteJson("/api/room/{$roomId}");

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                "message" => "Room deleted",
                "data" => []
            ]);

        $this->assertDatabaseCount('rooms', 0);
    }
}
