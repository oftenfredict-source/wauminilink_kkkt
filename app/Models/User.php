<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'can_approve_finances',
        'profile_picture',
        'phone_number',
        'login_blocked_until',
        'member_id',
        'campus_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'can_approve_finances' => 'boolean',
        'login_blocked_until' => 'datetime',
    ];

    // Helper methods for roles and permissions
    public function isPastor()
    {
        return $this->role === 'pastor' || $this->can_approve_finances;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isTreasurer()
    {
        return $this->role === 'treasurer';
    }

    public function canApproveFinances()
    {
        return $this->can_approve_finances || $this->isPastor() || $this->isAdmin() || $this->isSecretary();
    }

    public function canManageLeadership()
    {
        return $this->isPastor() || $this->role === 'secretary' || $this->isAdmin();
    }

    public function isSecretary()
    {
        return $this->role === 'secretary';
    }

    public function isMember()
    {
        return $this->role === 'member';
    }

    /**
     * Get the member associated with this user
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the campus associated with this user
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get the campus for this user
     * Returns the campus if user has one, otherwise tries to get from member
     */
    public function getCampus()
    {
        // First try to get campus directly from user
        if ($this->campus_id) {
            return $this->campus;
        }

        // Fallback: try to get campus from member
        if ($this->member_id && $this->member && $this->member->campus_id) {
            return $this->member->campus;
        }

        return null;
    }

    /**
     * Check if user is a Usharika admin
     * Usharika admin = admin role with main campus or no campus assigned
     */
    public function isUsharikaAdmin(): bool
    {
        if (!$this->isAdmin()) {
            return false;
        }

        $campus = $this->getCampus();

        // Admin with no campus or main campus is Usharika admin
        return !$campus || $campus->is_main_campus;
    }

    /**
     * Check if user is a branch user
     * Branch user = user assigned to a non-main campus
     */
    public function isBranchUser(): bool
    {
        $campus = $this->getCampus();
        return $campus && !$campus->is_main_campus;
    }

    /**
     * Check if user is an evangelism leader
     * Evangelism leader = user whose member has an active evangelism_leader position
     */
    public function isEvangelismLeader(): bool
    {
        if ($this->role === 'evangelism_leader') {
            return true;
        }

        if (!$this->member_id || !$this->member) {
            return false;
        }

        // Check if member has an active evangelism leader position
        $evangelismLeader = $this->member->activeLeadershipPositions()
            ->where('position', 'evangelism_leader')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->first();

        return $evangelismLeader !== null;
    }

    /**
     * Check if user is a parish worker
     * Parish worker = user whose member has an active parish_worker position
     */
    public function isParishWorker(): bool
    {
        if ($this->role === 'parish_worker') {
            return true;
        }

        if (!$this->member_id || !$this->member) {
            return false;
        }

        // Check if member has an active parish worker position
        $parishWorker = $this->member->activeLeadershipPositions()
            ->where('position', 'parish_worker')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->first();

        return $parishWorker !== null;
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permissionSlug): bool
    {
        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Roles to check
        $rolesToCheck = [$this->role];

        // Add implicit roles based on leadership positions
        if ($this->isEvangelismLeader()) {
            $rolesToCheck[] = 'evangelism_leader';
        }

        if ($this->isParishWorker()) {
            $rolesToCheck[] = 'parish_worker';
        }

        return \DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->whereIn('role_permissions.role', $rolesToCheck)
            ->where('permissions.slug', $permissionSlug)
            ->exists();
    }

    /**
     * Get all permissions for this user's role
     */
    public function getPermissions()
    {
        return \DB::table('permissions')
            ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', $this->role)
            ->select('permissions.*')
            ->get();
    }

    /**
     * Get activity logs for this user
     */
    public function activityLogs()
    {
        return $this->hasMany(\App\Models\ActivityLog::class);
    }

    /**
     * Get active sessions for this user
     */
    public function activeSessions()
    {
        return \DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>', now()->subHours(24)->timestamp)
            ->get();
    }

    /**
     * Check if user is a church elder
     */
    public function isChurchElder(): bool
    {
        if (!$this->member_id || !$this->member) {
            return false;
        }

        // Check if member has an active elder position
        $elder = $this->member->activeLeadershipPositions()
            ->where('position', 'elder')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->first();

        return $elder !== null;
    }

    /**
     * Get communities where this user is the church elder
     */
    public function elderCommunities()
    {
        if (!$this->member_id || !$this->member) {
            return collect();
        }

        // Get elder leadership positions
        $elderPositions = $this->member->activeLeadershipPositions()
            ->where('position', 'elder')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->get();

        // Get communities where this elder is assigned
        $communityIds = \App\Models\Community::whereIn('church_elder_id', $elderPositions->pluck('id'))->pluck('id');

        return \App\Models\Community::whereIn('id', $communityIds)->get();
    }

    /**
     * Check if user is currently blocked from logging in
     */
    public function isLoginBlocked(): bool
    {
        return $this->login_blocked_until && $this->login_blocked_until->isFuture();
    }

    /**
     * Get remaining block time in minutes
     * Returns the actual remaining time until the user can login again
     */
    public function getRemainingBlockTime(): ?int
    {
        if (!$this->login_blocked_until) {
            return null;
        }

        // Compare in UTC (how it's stored in database) for accuracy
        // Get raw timestamp from database to avoid timezone conversion issues
        $rawBlockedUntil = \Illuminate\Support\Facades\DB::table('users')
            ->where('id', $this->id)
            ->value('login_blocked_until');

        if (!$rawBlockedUntil) {
            return null;
        }

        // Parse as UTC directly (how it's stored in database)
        $blockedUntil = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $rawBlockedUntil, 'UTC');
        $now = \Carbon\Carbon::now('UTC');

        // If block has expired, return null
        if ($blockedUntil->lte($now)) {
            return null;
        }

        // Calculate remaining minutes: blockedUntil - now (both in UTC)
        // diffInMinutes with false returns signed value (positive if future)
        $remaining = $blockedUntil->diffInMinutes($now, false);

        // Ensure we return a positive value
        return $remaining > 0 ? (int) $remaining : null;
    }

    /**
     * Get remaining block time formatted (e.g., "2h 30m" or "45m")
     */
    public function getRemainingBlockTimeFormatted(): ?string
    {
        $minutes = $this->getRemainingBlockTime();

        if ($minutes === null) {
            return null;
        }

        if ($minutes < 60) {
            return "{$minutes}m";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes > 0) {
            return "{$hours}h {$remainingMinutes}m";
        }

        return "{$hours}h";
    }
}
