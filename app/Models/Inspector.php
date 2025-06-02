<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

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
        'designation_id',
        'qualifications',
        'professional_experience',
        'ib_clearance',    
        'ib_status_id',    
        'raw_clearance',   
        'raw_status_id',   
        'mea_clearance',
        'mea_status_id',
        'remarks',
        'is_draft',
        'is_reverted',
        'reverted_at',

    ];
    protected $dates = ['deleted_at'];










    // 🔒 Mutators - Encrypt before saving
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encryptString($value);
    }

    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = $value ? date('Y-m-d', strtotime($value)) : null;
    }
    


    public function setPassportNumberAttribute($value)
    {
        $this->attributes['passport_number'] = Crypt::encryptString($value);
    }

    public function setUnlpNumberAttribute($value)
    {
        $this->attributes['unlp_number'] = $value ? Crypt::encryptString($value) : null;
    }




    
    // 🔓 Accessors - Decrypt when retrieving
    public function getNameAttribute($value)
    {
        return $this->decryptValue($value);
    }
    
    public function getPassportNumberAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            \Log::error("Decryption failed: " . $e->getMessage());
            return $value; // Return original if it's not encrypted
        }
    }
    
    
    public function getUnlpNumberAttribute($value)
    {
        return $this->decryptValue($value);
    }

    private function decryptValue($value)
    {
        try {
            \Log::info('Trying to decrypt: ' . $value);
            $decrypted = Crypt::decryptString($value);
            \Log::info('Successfully decrypted: ' . $decrypted);
            return $decrypted;
        } catch (\Exception $e) {
            \Log::error('Decryption failed: ' . $e->getMessage());
            return $value; // Return original if decryption fails
        }
    }
    









     

    public function country()
    {
        return $this->belongsTo(related: Nationality::class)->withTrashed();
    }

    // Inspector.php
    public function rank()
    {
        return $this->belongsTo(Rank::class)->withTrashed();
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class)->withTrashed();
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
    public function ibStatus()
    {
        return $this->belongsTo(Status::class, 'ib_status_id'); // Adjust the foreign key if necessary
    }
    public function rawStatus()
    {
        return $this->belongsTo(Status::class, 'raw_status_id'); // Adjust the foreign key if necessary
    }
    public function meaStatus()
    {
        return $this->belongsTo(Status::class, 'mea_status_id'); // Adjust the foreign key if necessary
    }


    // Inspector.php
    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id'); // Assuming nationality_id is the foreign key
    }





public function setListOfInspectorsAttribute($value)
{
    $this->attributes['list_of_inspectors'] = Crypt::encryptString($value);
}

}
