<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = ['location_id', 'date', 'start_time', 'end_time', 'is_available'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
