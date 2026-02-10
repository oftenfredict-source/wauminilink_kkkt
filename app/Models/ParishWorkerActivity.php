<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParishWorkerActivity extends Model
{
    protected $fillable = [
        'user_id',
        'campus_id',
        'activity_type',
        'title',
        'description',
        'activity_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function getActivityTypeDisplayAttribute()
    {
        return match ($this->activity_type) {
            'altar_cleanliness' => 'Altar Cleanliness',
            'womens_department' => "Women's Department",
            'sunday_school' => "Sunday School (Sande School)",
            'holy_communion' => "Holy Communion (Lord's Supper)",
            'church_candles' => 'Church Candles',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->activity_type)),
        };
    }
}
