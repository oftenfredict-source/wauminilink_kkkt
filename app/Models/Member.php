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
        'campus_id',             // Campus association
        'community_id',           // Community association
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
        'wedding_date',
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
        'wedding_date' => 'date',
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

    // Campus relationship
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    // Community relationship
    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Generate a unique member ID
     * Format: YYYY + sequential (2 digits) + letter (1) + digits (2) + -WL
     * Example: 202574G58-WL
     */
    public static function generateMemberId()
    {
        do {
            $year = date('Y');
            
            // Get the highest sequential number for the current year
            // Extract sequential numbers from existing member IDs for this year
            $existingIds = self::where('member_id', 'LIKE', $year . '%')
                ->whereNotNull('member_id')
                ->pluck('member_id')
                ->toArray();
            
            $sequential = 1; // Start from 01
            if (!empty($existingIds)) {
                $maxSequential = 0;
                foreach ($existingIds as $id) {
                    // Extract the 2-digit sequential number (positions 4-5 after year)
                    // Format: YYYYXXL##-WL
                    if (strlen($id) >= 6 && substr($id, 0, 4) == $year) {
                        $seqPart = substr($id, 4, 2);
                        if (is_numeric($seqPart)) {
                            $seqNum = (int)$seqPart;
                            if ($seqNum > $maxSequential) {
                                $maxSequential = $seqNum;
                            }
                        }
                    }
                }
                $sequential = $maxSequential + 1;
            }
            
            // Ensure sequential is 2 digits (01-99)
            if ($sequential > 99) {
                $sequential = 1; // Reset to 01 if we exceed 99 (unlikely but safety check)
            }
            $sequentialPadded = str_pad($sequential, 2, '0', STR_PAD_LEFT);
            
            // Generate 1 random uppercase letter (A-Z)
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomLetter = $letters[rand(0, strlen($letters) - 1)];
            
            // Generate 2 random digits (00-99)
            $randomDigits = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
            
            $memberId = $year . $sequentialPadded . $randomLetter . $randomDigits . '-WL';
            
        } while (self::where('member_id', $memberId)->exists());
        
        return $memberId;
    }

    /**
     * Generate a unique biometric enroll ID (2-3 digits: 10-999)
     * This ID is used to register members on the biometric device
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
                for ($id = 10; $id <= 999; $id++) {
                    if (!self::where('biometric_enroll_id', (string)$id)->exists()) {
                        return (string)$id;
                    }
                }
                throw new \Exception('Cannot generate unique biometric enroll ID. All IDs (10-999) are taken.');
            }
            
        } while (self::where('biometric_enroll_id', (string)$enrollId)->exists());
        
        return (string)$enrollId;
    }

    /**
     * Boot method to auto-generate biometric enroll ID when member is created
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            // Auto-generate biometric enroll ID if not provided
            if (empty($member->biometric_enroll_id)) {
                $member->biometric_enroll_id = self::generateBiometricEnrollId();
            }
        });
    }
}