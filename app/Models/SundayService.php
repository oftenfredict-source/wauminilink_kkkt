<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SundayService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_date',
        'theme',
        'preacher',
        'start_time',
        'end_time',
        'venue',
        'attendance_count',
        'offerings_amount',
        'scripture_readings',
        'choir',
        'announcements',
        'notes',
    ];

    protected $casts = [
        'service_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'offerings_amount' => 'decimal:2',
        'attendance_count' => 'integer',
    ];
}


