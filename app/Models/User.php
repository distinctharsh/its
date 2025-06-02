<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
 
        'designation_id', 
        'phone_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];




     // 🔒 Mutators - Encrypt fields before saving

    // Encrypt Name
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Crypt::encryptString($value);
    }

    // Encrypt Email
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = Crypt::encryptString($value);
    }

    // Encrypt Phone Number
    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = Crypt::encryptString($value);
    }

   // 🔓 Accessors - Decrypt fields when retrieving

   public function getNameAttribute($value)
   {
       return $this->decryptIfEncrypted($value);
   }

   public function getEmailAttribute($value)
   {
       return $this->decryptIfEncrypted($value);
   }

   public function getPhoneNumberAttribute($value)
   {
       return $this->decryptIfEncrypted($value);
   }

   /**
    * Try to decrypt the value if it appears to be encrypted.
    *
    * @param string|null $value
    * @return string|null
    */
   private function decryptIfEncrypted($value)
   {
       try {
           // Attempt to decrypt
           return Crypt::decryptString($value);
       } catch (DecryptException $e) {
           // If decryption fails (i.e., it's not encrypted), return the original value
           return $value;
       }
   }

   public function scopeFindByDecryptedEmail($query, $email)
{
    return $query->get()->firstWhere('email', $email);
}



    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function hasRole($role)
    {
        return strtolower($this->role->name) === strtolower($role);
    }
}
