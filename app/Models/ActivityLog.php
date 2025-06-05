<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Specify the table name
    protected $table = 'activity_logs';

    // Define the fillable attributes
    protected $fillable = [
        'user_id',
        'ip_addr',
        'action_type',
        'affected_table',
        'record_id',
        'changes',
        'created_at',
    ];

    // Set primary key and incrementing option
    protected $primaryKey = 'id';
    public $incrementing = true;
    
    // Define timestamps if they are not managed by Laravel
    public $timestamps = false;

    // Set the casting of JSON data for 'changes' column
    protected $casts = [
        'changes' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
