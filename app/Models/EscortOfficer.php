<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EscortOfficer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'officer_name',
    ];

    public function visits()
    {
        return $this->belongsToMany(Visit::class, 'visit_escort_officer');
    }
}
