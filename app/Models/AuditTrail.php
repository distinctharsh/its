<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $table = 'audit_trails';
    public $timestamps = false; // No updated_at column

    protected $fillable = [
        'user_id',
        'username',
        'ip_addr',
        'status',
        'action_details',
        'created_at',
    ];
}
