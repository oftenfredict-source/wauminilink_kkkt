<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name',
        'description',
        'criteria',
        'created_by',
    ];

    protected $casts = [
        'criteria' => 'array',
    ];

    /**
     * Check if a member or child is eligible for this department.
     *
     * @param mixed $person Member or Child
     * @return array ['eligible' => bool, 'reason' => string|null]
     */
    public function checkEligibility($person)
    {
        $criteria = $this->criteria;
        if (empty($criteria)) {
            return ['eligible' => true, 'reason' => null];
        }

        // Check Age
        if (isset($criteria['min_age']) || isset($criteria['max_age'])) {
            $age = $person->date_of_birth ? \Carbon\Carbon::parse($person->date_of_birth)->age : null;
            
            if ($age === null) {
                return ['eligible' => false, 'reason' => 'Date of birth is missing.'];
            }

            if (isset($criteria['min_age']) && $age < $criteria['min_age']) {
                return ['eligible' => false, 'reason' => "Person is too young. Minimum age is {$criteria['min_age']}."];
            }

            if (isset($criteria['max_age']) && $age > $criteria['max_age']) {
                return ['eligible' => false, 'reason' => "Person is too old. Maximum age is {$criteria['max_age']}."];
            }
        }

        // Check Gender
        if (isset($criteria['gender']) && strtolower($person->gender ?? '') !== strtolower($criteria['gender'])) {
            return ['eligible' => false, 'reason' => "Gender mismatch. Required: " . ucfirst($criteria['gender']) . "."];
        }

        // These checks only apply to adult members
        if ($person instanceof Member) {
            // Check Marital Status
            if (isset($criteria['marital_status'])) {
                $memberStatus = strtolower($person->marital_status ?? '');
                $requiredStatus = strtolower($criteria['marital_status']);
                
                if ($memberStatus !== $requiredStatus) {
                    return ['eligible' => false, 'reason' => "Marital status mismatch. Required: " . ucfirst($requiredStatus) . "."];
                }
            }

            // Check Children
            if (isset($criteria['has_children']) && $criteria['has_children'] === true) {
                $childrenCount = $person->all_children ? $person->all_children->count() : 0;

                if ($childrenCount < 1) {
                    return ['eligible' => false, 'reason' => "Member must have at least one child."];
                }
            }
        }

        return ['eligible' => true, 'reason' => null];
    }
    public function members()
    {
        return $this->belongsToMany(Member::class, 'department_member')
                    ->withPivot('status', 'assigned_at')
                    ->withTimestamps();
    }

    public function children()
    {
        return $this->belongsToMany(Child::class, 'department_member')
                    ->withPivot('status', 'assigned_at')
                    ->withTimestamps();
    }
}
