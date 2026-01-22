<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leader extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'campus_id',
        'position',
        'position_title',
        'description',
        'appointment_date',
        'end_date',
        'is_active',
        'appointed_by',
        'notes'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationship to Member
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Relationship to Campus
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    // Relationship to Communities (as church elder)
    public function communities()
    {
        return $this->hasMany(Community::class, 'church_elder_id')->withTrashed();
    }

    // Relationship to Weekly Assignments
    public function weeklyAssignments()
    {
        return $this->hasMany(WeeklyAssignment::class);
    }

    // Scope for active leaders
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific position
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    // Get position display name in Swahili
    public function getPositionDisplayAttribute()
    {
        $positions = [
            'pastor' => 'Mchungaji',
            'assistant_pastor' => 'Msaidizi wa Mchungaji',
            'secretary' => 'Katibu',
            'assistant_secretary' => 'Msaidizi wa Katibu',
            'treasurer' => 'Mweka Hazina',
            'assistant_treasurer' => 'Msaidizi wa Mweka Hazina',
            'elder' => 'Mzee wa Kanisa',
            'deacon' => 'Shamashi',
            'deaconess' => 'Shamasha',
            'youth_leader' => 'Kiongozi wa Vijana',
            'children_leader' => 'Kiongozi wa Watoto',
            'worship_leader' => 'Kiongozi wa Ibada',
            'choir_leader' => 'Kiongozi wa Kwaya',
            'usher_leader' => 'Kiongozi wa Wakaribishaji',
            'evangelism_leader' => 'Kiongozi wa Uinjilisti',
            'prayer_leader' => 'Kiongozi wa Maombi',
            'other' => $this->position_title ?? 'Kiongozi'
        ];

        return $positions[$this->position] ?? $this->position_title ?? 'Haijulikani';
    }

    // Check if position is currently active
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->toDateString();
        
        if ($this->end_date && $this->end_date < $now) {
            return false;
        }

        return true;
    }
}
