<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspector extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inspectors';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'name',
        'gender_id',
        'dob',
        'nationality_id',
        'place_of_birth',
        'passport_number',
        'unlp_number',
        'rank_id',
        'qualifications',
        'professional_experience',
        'clearance_certificate',
        'remarks',
        'is_active'
    ];
    protected $dates = ['deleted_at'];

    public function country()
    {
        return $this->belongsTo(related: Nationality::class)->withTrashed();
    }

    // Inspector.php
    public function rank()
    {
        return $this->belongsTo(Rank::class)->withTrashed();
    }


    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'inspector_id'); // Ensure 'inspector_id' is the foreign key in the inspections table
    }

    // Define the relationship to visits
    public function visits()
    {
        return $this->hasMany(Visit::class, 'inspector_id'); // Ensure 'inspector_id' is the foreign key in the visits table
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id'); // Adjust the foreign key if necessary
    }
    // Inspector.php
    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id'); // Assuming nationality_id is the foreign key
    }
}