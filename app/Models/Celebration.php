<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Celebration extends Model
{
    protected $fillable = [
        'title',
        'description',
        'celebration_date',
        'start_time',
        'end_time',
        'venue',
        'type',
        'celebrant_name',
        'expected_guests',
        'budget',
        'special_requests',
        'notes',
        'is_public'
    ];

    protected $casts = [
        'celebration_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'expected_guests' => 'integer',
        'budget' => 'decimal:2',
        'is_public' => 'boolean'
    ];
}
