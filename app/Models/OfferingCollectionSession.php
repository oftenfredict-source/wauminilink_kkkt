<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferingCollectionSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'collection_date',
        'campus_id',
        'lead_elder_id',
        'status',
        'total_amount',
        'received_by',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'collection_date' => 'date',
        'total_amount' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function leadElder()
    {
        return $this->belongsTo(User::class, 'lead_elder_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(OfferingCollectionItem::class);
    }
}
