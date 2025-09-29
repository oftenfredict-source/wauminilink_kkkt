<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'member_snapshot',
        'reason',
        'deleted_at_actual',
    ];

    protected $casts = [
        'member_snapshot' => 'array',
        'deleted_at_actual' => 'datetime',
    ];
}



