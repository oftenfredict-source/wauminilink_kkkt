<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParishWorkerReport extends Model
{
    protected $fillable = [
        'user_id',
        'campus_id',
        'title',
        'content',
        'report_period_start',
        'report_period_end',
        'submitted_at',
        'status',
        'pastor_comments',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'report_period_start' => 'date',
        'report_period_end' => 'date',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
