<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'full_name',
        'gender',
        'date_of_birth',
        'biometric_enroll_id',
        'parent_name',
        'parent_phone',
        'parent_relationship',
        'baptism_status',
        'baptism_date',
        'baptism_location',
        'baptized_by',
        'baptism_certificate_number',
        'is_church_member',
        'campus_id',
        'community_id',
        'region',
        'district',
        'city_town',
        'current_church_attended',
        'phone_number',
        'lives_outside_main_area',
        'orphan_status',
        'disability_status',
        'disability_type',
        'vulnerable_status',
        'vulnerable_type',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'baptism_date' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_member')
                    ->withPivot('status', 'assigned_at')
                    ->withTimestamps();
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function transitions()
    {
        return $this->hasMany(ChildToMemberTransition::class);
    }

    public function pendingTransition()
    {
        return $this->hasOne(ChildToMemberTransition::class)->where('status', 'pending');
    }

    public function isEligibleForTransition()
    {
        if (!$this->is_church_member || !$this->date_of_birth) {
            return false;
        }
        
        $age = $this->getAge();
        if ($age < 18) {
            return false;
        }

        // Check if there's already a pending or approved transition
        return !$this->transitions()
            ->whereIn('status', ['pending', 'approved', 'completed'])
            ->exists();
    }

    /**
     * Get parent/guardian name (from member or non-member parent)
     */
    public function getParentName()
    {
        if ($this->member) {
            return $this->member->full_name;
        }
        return $this->parent_name ?? null;
    }

    /**
     * Get parent/guardian phone (from member or non-member parent)
     */
    public function getParentPhone()
    {
        if ($this->member) {
            return $this->member->phone_number;
        }
        return $this->parent_phone ?? null;
    }

    /**
     * Get parent/guardian relationship
     */
    public function getParentRelationship()
    {
        if ($this->member) {
            return 'Church Member';
        }
        return $this->parent_relationship ?? null;
    }

    /**
     * Check if parent is a church member
     */
    public function hasMemberParent()
    {
        return !is_null($this->member_id);
    }

    /**
     * Get parents who are church members (linked parent + their spouse/main member)
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getMemberParents()
    {
        $parents = collect();
        if ($this->member) {
            $parents->push($this->member);
            
            // Check for spouse if they are also a church member
            if ($this->member->spouse_member_id && $this->member->spouseMember) {
                $parents->push($this->member->spouseMember);
            }
            
            // Check if current linked member is a spouse of another member
            if ($this->member->mainMember) {
                $parents->push($this->member->mainMember);
            }
        }
        return $parents->unique('id')->values();
    }

    /**
     * Get attendance records for this child
     */
    public function attendances()
    {
        return $this->hasMany(ServiceAttendance::class);
    }

    /**
     * Get Sunday service attendance records
     */
    public function sundayServiceAttendances()
    {
        return $this->hasMany(ServiceAttendance::class)->sundayServices();
    }

    /**
     * Get special event attendance records
     */
    public function specialEventAttendances()
    {
        return $this->hasMany(ServiceAttendance::class)->specialEvents();
    }

    /**
     * Calculate the child's age
     * 
     * @param Carbon|null $referenceDate Reference date for age calculation (defaults to today)
     * @return int
     */
    public function getAge($referenceDate = null)
    {
        if (!$this->date_of_birth) {
            return 0;
        }

        $reference = $referenceDate ?? Carbon::now();
        // Use diffInYears which returns integer, but ensure it's cast to int for safety
        return (int) $this->date_of_birth->diffInYears($reference);
    }

    /**
     * Get the child's age group
     * 
     * @return string|null 'infant' (< 3), 'sunday_school' (3-12), 'teenager' (13-17), or null if 18+
     */
    public function getAgeGroup()
    {
        $age = $this->getAge();
        
        if ($age < 3) {
            return 'infant';
        } elseif ($age >= 3 && $age <= 12) {
            return 'sunday_school';
        } elseif ($age >= 13 && $age <= 17) {
            return 'teenager';
        }
        
        return null; // 18 or older
    }

    /**
     * Determine which service type this child should attend based on age
     * 
     * @return string|null 'children_service' for ages 3-12, 'sunday_service' for ages 13-17, null for others
     */
    public function getRecommendedServiceType()
    {
        $ageGroup = $this->getAgeGroup();
        
        switch ($ageGroup) {
            case 'sunday_school':
                return 'children_service'; // Sunday School
            case 'teenager':
                return 'sunday_service'; // Main adult service
            default:
                return null; // Infants (< 3) or adults (18+) - not typically recorded
        }
    }

    /**
     * Check if this child should attend Sunday School (ages 3-12)
     * 
     * @return bool
     */
    public function shouldAttendSundaySchool()
    {
        return $this->getAgeGroup() === 'sunday_school';
    }

    /**
     * Check if this child should attend main service (ages 13-17)
     * 
     * @return bool
     */
    public function shouldAttendMainService()
    {
        return $this->getAgeGroup() === 'teenager';
    }

    /**
     * Check if this child is part of children's ministry (ages 3-17)
     * 
     * @return bool
     */
    public function isChildrenMinistryMember()
    {
        $ageGroup = $this->getAgeGroup();
        return in_array($ageGroup, ['sunday_school', 'teenager']);
    }

    /**
     * Check if this child should be recorded in attendance (ages 3-17)
     * 
     * @return bool
     */
    public function shouldRecordAttendance()
    {
        $ageGroup = $this->getAgeGroup();
        return in_array($ageGroup, ['sunday_school', 'teenager']);
    }

    /**
     * Generate a unique biometric enroll ID (2-3 digits: 10-999)
     * This ID is used to register children (teenagers) on the biometric device
     * 
     * @return string Unique enroll ID between 10 and 999
     */
    public static function generateBiometricEnrollId()
    {
        $maxAttempts = 1000; // Prevent infinite loop
        $attempts = 0;
        
        do {
            // Generate random number between 10 and 999 (2-3 digits)
            $enrollId = rand(10, 999);
            $attempts++;
            
            if ($attempts >= $maxAttempts) {
                // If we can't find a unique ID, try sequential search
                // Check both members and children tables
                for ($id = 10; $id <= 999; $id++) {
                    $existsInMembers = \App\Models\Member::where('biometric_enroll_id', (string)$id)->exists();
                    $existsInChildren = self::where('biometric_enroll_id', (string)$id)->exists();
                    
                    if (!$existsInMembers && !$existsInChildren) {
                        return (string)$id;
                    }
                }
                throw new \Exception('Cannot generate unique biometric enroll ID. All IDs (10-999) are taken.');
            }
            
        } while (
            \App\Models\Member::where('biometric_enroll_id', (string)$enrollId)->exists() ||
            self::where('biometric_enroll_id', (string)$enrollId)->exists()
        );
        
        return (string)$enrollId;
    }

    /**
     * Boot method to auto-generate biometric enroll ID when child is created
     * Only for teenagers (13-17) who should attend main service
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($child) {
            // Auto-generate biometric enroll ID for teenagers who should record attendance
            // Only generate if child is a teenager (13-17) who should attend main service
            if (empty($child->biometric_enroll_id) && $child->shouldAttendMainService()) {
                $child->biometric_enroll_id = self::generateBiometricEnrollId();
            }
        });
    }
}



