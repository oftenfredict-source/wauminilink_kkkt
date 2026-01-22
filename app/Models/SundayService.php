<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SundayService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_date',
        'service_type',
        'status',
        'theme',
        'preacher',
        'coordinator_id',
        'church_elder_id',
        'campus_id',
        'evangelism_leader_id',
        'is_branch_service',
        'start_time',
        'end_time',
        'venue',
        'attendance_count',
        'guests_count',
        'offerings_amount',
        'scripture_readings',
        'choir',
        'announcements',
        'notes',
    ];

    protected $casts = [
        'service_date' => 'date',
        'start_time' => 'string', // Store as time string (HH:MM)
        'end_time' => 'string', // Store as time string (HH:MM)
        'offerings_amount' => 'decimal:2',
        'attendance_count' => 'integer',
        'guests_count' => 'integer',
    ];

    /**
     * Get the coordinator member
     */
    public function coordinator()
    {
        return $this->belongsTo(Member::class, 'coordinator_id');
    }

    /**
     * Get the church elder member
     */
    public function churchElder()
    {
        return $this->belongsTo(Member::class, 'church_elder_id');
    }

    /**
     * Get attendance records for this service
     */
    public function attendances()
    {
        return $this->hasMany(ServiceAttendance::class, 'service_id')
            ->where('service_type', 'sunday_service');
    }

    /**
     * Get members who attended this service
     */
    public function attendingMembers()
    {
        return $this->belongsToMany(Member::class, 'service_attendances', 'service_id', 'member_id')
            ->wherePivot('service_type', 'sunday_service')
            ->withPivot('attended_at', 'recorded_by', 'notes')
            ->withTimestamps();
    }

    /**
     * Get promise guests for this service
     */
    public function promiseGuests()
    {
        return $this->hasMany(PromiseGuest::class, 'service_id');
    }

    /**
     * Get the campus (for branch services)
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the evangelism leader (for branch services)
     */
    public function evangelismLeader()
    {
        return $this->belongsTo(User::class, 'evangelism_leader_id');
    }

    /**
     * Get branch offerings for this service
     */
    public function branchOfferings()
    {
        return $this->hasMany(BranchOffering::class, 'service_id');
    }
}


