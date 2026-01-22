<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Community extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'address',
        'region',
        'district',
        'ward',
        'phone_number',
        'email',
        'campus_id',
        'is_active',
        'church_elder_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the campus that owns this community
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    /**
     * Get all members belonging to this community
     */
    public function members()
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Scope to get only active communities
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the church elder for this community
     */
    public function churchElder()
    {
        return $this->belongsTo(Leader::class, 'church_elder_id');
    }
}
