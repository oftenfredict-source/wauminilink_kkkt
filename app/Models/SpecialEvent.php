<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_date','title','speaker','start_time','end_time','venue',
        'attendance_count','budget_amount','category','description','notes'
    ];

    protected $casts = [
        'event_date' => 'date',
        'attendance_count' => 'integer',
        'budget_amount' => 'decimal:2',
    ];
}



