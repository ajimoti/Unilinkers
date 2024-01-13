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
    public function __construct(Property $property, int $perPage = null)
    {
        $this->property = $property;
        $this->perPage = $perPage ?? config('response.per_page');
    }

    /**
     * Get all properties.
     */
    public function index(GetPropertiesRequest $request): JsonResponse
    {
        $request->validated();

        $properties = $this->property->with('rooms')->paginate($request->per_page ?? $this->perPage);

        return json('Properties retrieved', new PropertyCollection($properties));
    }

    /**
     * Store a newly created property.
     */
    public function store(StorePropertyRequest $request): JsonResponse
    {
        // Using firstOrCreate to avoid duplicate entries
        $property = $this->property->firstOrCreate($request->validated());

        return json('Property created', new PropertyResource($property), 201);
    }

    /**
     * Show the specified resource in storage.
     */
    public function show(Property $property)
    {
        return json('Property detail', new PropertyResource($property));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, Property $property)
    {
        $property->update($request->validated());

        return json('Property updated', new PropertyResource($property));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();

        return json('Property deleted');
    }
}
