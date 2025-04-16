<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{

    protected $fillable = [
        'user_id',
        'time_slot_id',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class)
            ->withTimestamps();
    }
    public function timeSlot()
    {
        return $this->belongsToMany(TimeSlot::class);
    }

    public function timeSlots()
    {
        return $this->belongsToMany(TimeSlot::class)
            ->withTimestamps()
            ->with('location');

    }
}
