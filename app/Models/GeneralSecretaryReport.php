<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSecretaryReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'income_estimates',
        'expense_estimates',
        'status',
        'verified_by',
        'verified_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'income_estimates' => 'array',
        'expense_estimates' => 'array',
        'verified_at' => 'datetime',
    ];

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isVerified()
    {
        return $this->status === 'verified';
    }
}
