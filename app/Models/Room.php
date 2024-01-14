<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Returns the human readable size of the room.
     *
     * @return string
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Returns the human readable size of the room.
     *
     * @return string
     */
    protected function humanReadableSize(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->size} {$this->size_unit}",
        );
    }
}
