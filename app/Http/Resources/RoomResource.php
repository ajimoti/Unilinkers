<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'name' => $this->name,
            'size' => $this->size,
            'size_unit' => $this->size_unit,
            'human_readable_size' => $this->human_readable_size,
            'property' => new PropertyResource($this->whenLoaded('property')),
        ];
    }
}
