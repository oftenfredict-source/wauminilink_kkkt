<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandleAction extends Model
{
    protected $fillable = [
        'user_id',
        'campus_id',
        'action_type',
        'quantity',
        'cost',
        'action_date',
        'received_by',
        'notes',
    ];

    protected $casts = [
        'action_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
