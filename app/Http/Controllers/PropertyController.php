<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\GetPropertiesRequest;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\PropertyCollection;

class PropertyController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        public Property $property,
        public int $perPage,
    ){
        $this->perPage = $perPage ?? config('response.per_page');
    }

    /**
     * Get all properties.
     */
    public function index(GetPropertiesRequest $request): JsonResponse
    {
        try {
            $request->validated();

            $properties = $this->property->paginate($request->per_page ?? $this->perPage);

            return json('Properties retrieved', new PropertyCollection($properties));
        } catch (\Throwable $th) {
            return json_message('Error retrieving properties', 500);
        }
    }

    /**
     * Store a newly created property.
     */
    public function store(StorePropertyRequest $request): JsonResponse
    {
        try {
            // Using firstOrCreate to avoid duplicate entries
            $property = $this->property->firstOrCreate($request->validated());

            return json('Property created', new PropertyResource($property), 201);
        } catch (\Throwable $th) {
            return json_message('Error creating property', 500);
        }
    }

    /**
     * Show the specified resource in storage.
     */
    public function show(Property $property)
    {
        return json('Property updated', new PropertyResource($property));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        try {
            $property->update($request->validated());

            return json('Property updated', new PropertyResource($property));
        } catch (\Throwable $th) {
            return json_message('Error updating property', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        try {
            $property->delete();

            return json('Property deleted');
        } catch (\Throwable $th) {
            return json_message('Error deleting property', 500);
        }
    }
}
