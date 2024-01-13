<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    protected function humanReadableSize(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->size} {$this->size_unit}",
        );
    }
}
