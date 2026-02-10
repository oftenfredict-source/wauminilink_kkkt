<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhadiPledge extends Model
{
    protected $fillable = [
        'member_id',
        'community_id',
        'campus_id',
        'year',
        'item_type',
        'quantity_promised',
        'unit',
        'estimated_value',
        'quantity_fulfilled',
        'fulfillment_date',
        'status',
        'recorded_by',
        'notes'
    ];

    protected $casts = [
        'quantity_promised' => 'decimal:2',
        'quantity_fulfilled' => 'decimal:2',
        'estimated_value' => 'decimal:2',
        'fulfillment_date' => 'date',
    ];

    const ITEMS = [
        'Ng\'ombe', 'Mbuzi', 'Kondoo', 'Kuku', 'Maziwa', 'Mayai', 
        'Kahawa', 'Ndizi', 'Mahindi', 'Maharagwe', 'Makopa', 'Mboga', 
        'Miwa', 'Ngano', 'Vifaa vya Sanaa', 'Bidhaa za Ufundi', 
        'Bidhaa za Viwanda', 'Vinginevyo', 'Fedha (Cash)'
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    // Progress calculation
    public function getProgressPercentageAttribute()
    {
        if ($this->quantity_promised == 0) return 0;
        return round(($this->quantity_fulfilled / $this->quantity_promised) * 100, 2);
    }
}
