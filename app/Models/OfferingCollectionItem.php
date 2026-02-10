<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferingCollectionItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'offering_collection_session_id',
        'community_id',
        'amount_unity',
        'amount_building',
        'amount_pledge',
        'amount_other',
        'notes',
    ];

    protected $casts = [
        'amount_unity' => 'decimal:2',
        'amount_building' => 'decimal:2',
        'amount_pledge' => 'decimal:2',
        'amount_other' => 'decimal:2',
    ];

    public function session()
    {
        return $this->belongsTo(OfferingCollectionSession::class, 'offering_collection_session_id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
