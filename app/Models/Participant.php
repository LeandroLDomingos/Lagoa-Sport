<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cpf',
        'rg',
        'contact',
        'email',
    ];

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class)
                    ->withTimestamps();
    }
}
