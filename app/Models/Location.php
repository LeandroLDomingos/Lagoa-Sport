<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'min_participants'];

    public function images()
    {
        return $this->hasMany(LocationImages::class);
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }
}
