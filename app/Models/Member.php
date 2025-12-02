<?php
// File: app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Member extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'member_id',
        'biometric_enroll_id',
        'member_type',           // father, mother, independent
        'membership_type',       // permanent, temporary
        'full_name',
        'email',
        'phone_number',
        'date_of_birth',
        'gender',
        'education_level',       // primary, secondary, chuo_cha_kati, university
        'profession',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
        'nida_number',
        'tribe',
        'other_tribe',
        'region',
        'district',
        'ward',
        'street',
        'address',
        'residence_region',
        'residence_district',
        'residence_ward',
        'residence_street',
        'residence_road',
        'residence_house_number',
        'profile_picture',
        'marital_status',
        'spouse_full_name',
        'spouse_date_of_birth',
        'spouse_education_level',
        'spouse_profession',
        'spouse_nida_number',
        'spouse_email',
        'spouse_phone_number',
        'spouse_tribe',
        'spouse_other_tribe',
        'spouse_gender',
        'spouse_church_member',
        'spouse_member_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'spouse_date_of_birth' => 'date',
    ];

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    // Relationship to spouse member (if spouse is also a church member)
    public function spouseMember()
    {
        return $this->belongsTo(Member::class, 'spouse_member_id');
    }

    // Reverse relationship - get the main member from spouse member
    public function mainMember()
    {
        return $this->hasOne(Member::class, 'spouse_member_id');
    }

    // Financial relationships
    public function tithes()
    {
        return $this->hasMany(Tithe::class);
    }

    public function offerings()
    {
        return $this->hasMany(Offering::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }

    // Attendance relationships
    public function attendances()
    {
        return $this->hasMany(ServiceAttendance::class);
    }

    public function sundayServiceAttendances()
    {
        return $this->hasMany(ServiceAttendance::class)->sundayServices();
    }

    public function specialEventAttendances()
    {
        return $this->hasMany(ServiceAttendance::class)->specialEvents();
    }

    // Leadership relationships
    public function leadershipPositions()
    {
        return $this->hasMany(Leader::class);
    }

    public function activeLeadershipPositions()
    {
        return $this->hasMany(Leader::class)->where('is_active', true);
    }

    // Bereavement relationships
    public function bereavementContributions()
    {
        return $this->hasMany(BereavementContribution::class);
    }

    // User account relationship
    public function user()
    {
        return $this->hasOne(User::class);
    }

    /**
     * Generate a unique member ID
     * Format: YYYY + random alphanumeric (5 chars) + -WL
     * Example: 2025A3B7C-WL
     */
    public static function generateMemberId()
    {
        do {
            $year = date('Y');
            $randomPart = '';
            
            // Generate 5 random alphanumeric characters
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            for ($i = 0; $i < 5; $i++) {
                $randomPart .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            $memberId = $year . $randomPart . '-WL';
            
        } while (self::where('member_id', $memberId)->exists());
        
        return $memberId;
    }
}