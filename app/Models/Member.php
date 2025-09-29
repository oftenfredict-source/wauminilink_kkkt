<?php
// File: app/Models/Member.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
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
        'living_with_family',
        'family_relationship',
        'profile_picture',
            'mother_alive',
            'mother_full_name',
            'mother_date_of_birth',
            'mother_education_level',
            'mother_profession',
            'mother_nida_number',
            'mother_email',
            'mother_phone_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
            'mother_date_of_birth' => 'date',
    ];

    public function children()
    {
        return $this->hasMany(Child::class);
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