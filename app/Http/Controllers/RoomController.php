<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Models\Property;

class RoomController extends Controller
{
    /**
     * Property model.
     */
    public Property $property;

    /**
     * Number of items per page.
     */
    public int $perPage;

    /**
     * Create a new controller instance.
     */
    public function __construct(Property $property,int $perPage = null)
    {
        $this->property = $property;
        $this->perPage = $perPage ?? config('response.per_page');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Property $property)
    {
        return json('Rooms retrieved', RoomResource::collection($property->rooms));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        $validated = $request->validated();

        $property = $this->property->findOrFail($request->property_id);

        $room = $property->rooms()->firstOrCreate($validated);

        return json('Room created', new RoomResource($room), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());

        return json('Room updated', new RoomResource($room));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return json('Room deleted');
    }
}
