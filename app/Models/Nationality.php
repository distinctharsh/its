<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nationality extends Model
{


    use HasFactory, SoftDeletes;

    protected $table = 'nationalities';
    protected $primaryKey = 'id'; 
    public $timestamps = true; 

    protected $fillable = [
        'country_name',
    ];

}
