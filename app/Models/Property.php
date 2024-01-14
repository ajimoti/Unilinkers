<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Returns the rooms of the property.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Room>
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
