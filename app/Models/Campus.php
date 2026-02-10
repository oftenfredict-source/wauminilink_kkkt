<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'address',
        'region',
        'district',
        'ward',
        'phone_number',
        'email',
        'parent_id',
        'is_main_campus',
        'is_active',
        'evangelism_leader_id',
    ];

    protected $casts = [
        'is_main_campus' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent campus (main campus for sub campuses)
     */
    public function parent()
    {
        return $this->belongsTo(Campus::class, 'parent_id');
    }

    /**
     * Get all sub campuses
     */
    public function subCampuses()
    {
        return $this->hasMany(Campus::class, 'parent_id');
    }

    /**
     * Get all active sub campuses
     */
    public function activeSubCampuses()
    {
        return $this->hasMany(Campus::class, 'parent_id')->where('is_active', true);
    }

    /**
     * Get all members belonging to this campus
     */
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get all children who are church members belonging to this campus
     */
    public function memberChildren()
    {
        return $this->hasMany(Child::class)->where('is_church_member', true);
    }

    /**
     * Get all users associated with this campus
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all communities belonging to this campus
     */
    public function communities()
    {
        return $this->hasMany(Community::class);
    }

    /**
     * Get all active communities belonging to this campus
     */
    public function activeCommunities()
    {
        return $this->hasMany(Community::class)->where('is_active', true);
    }

    /**
     * Get the evangelism leader for this campus
     */
    public function evangelismLeader()
    {
        return $this->belongsTo(Leader::class, 'evangelism_leader_id');
    }

    /**
     * Get the main campus (if this is a sub campus)
     */
    public function getMainCampus()
    {
        if ($this->is_main_campus) {
            return $this;
        }
        
        return $this->parent ? $this->parent->getMainCampus() : null;
    }

    /**
     * Check if this is a sub campus
     */
    public function isSubCampus(): bool
    {
        return !$this->is_main_campus && $this->parent_id !== null;
    }

    /**
     * Get all members including from sub campuses (for main campus)
     */
    public function getAllMembers()
    {
        $memberIds = $this->members()->pluck('id');
        
        // Include members from sub campuses
        foreach ($this->subCampuses as $subCampus) {
            $memberIds = $memberIds->merge($subCampus->members()->pluck('id'));
        }
        
        return Member::whereIn('id', $memberIds);
    }

    /**
     * Scope to get only main campuses
     */
    public function scopeMainCampuses($query)
    {
        return $query->where('is_main_campus', true);
    }

    /**
     * Scope to get only sub campuses
     */
    public function scopeSubCampuses($query)
    {
        return $query->where('is_main_campus', false)->whereNotNull('parent_id');
    }

    /**
     * Scope to get only active campuses
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate a unique campus code
     */
    public static function generateCode($name, $parentId = null)
    {
        // If it's a main campus, use MAIN
        if ($parentId === null) {
            $code = 'MAIN';
            if (self::where('code', $code)->exists()) {
                throw new \Exception('Main campus already exists');
            }
            return $code;
        }

        // For sub campuses, generate SUB-XXX format
        $prefix = 'SUB';
        $counter = 1;
        
        do {
            $code = $prefix . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        } while (self::where('code', $code)->exists() && $counter < 1000);
        
        return $code;
    }
}



