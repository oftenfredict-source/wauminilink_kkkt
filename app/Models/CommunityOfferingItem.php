<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityOfferingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_offering_id',
        'member_id',
        'envelope_number',
        'amount',
        'amount_umoja',
        'amount_jengo',
        'amount_ahadi',
        'amount_other',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_umoja' => 'decimal:2',
        'amount_jengo' => 'decimal:2',
        'amount_ahadi' => 'decimal:2',
        'amount_other' => 'decimal:2',
    ];

    public function offering()
    {
        return $this->belongsTo(CommunityOffering::class, 'community_offering_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
