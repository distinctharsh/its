<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'gender_id',
        'dob',
        'nationality_id',
        'place_of_birth',
        'passport_number',
        'unlp_number',
        'rank_id',
        'designation_id',
        'qualifications',
        'professional_experience',
        'scope_of_access',
        'security_status',    
        'opcw_communication_date',   
        'deletion_date',
        'remarks',
            'is_draft',
        'is_reverted',
        'reverted_at',

    ];




    public function rank()
    {
        return $this->belongsTo(Rank::class)->withTrashed();
    }
    public function status()
    {
        return $this->belongsTo(Status::class, 'security_status'); 
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class)->withTrashed();
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
