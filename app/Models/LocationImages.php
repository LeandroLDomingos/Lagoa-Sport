<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationImages extends Model
{
    protected $fillable = ['location_id', 'image'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}


