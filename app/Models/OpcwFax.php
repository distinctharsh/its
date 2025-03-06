<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpcwFax extends Model
{
    use HasFactory, SoftDeletes;


    // Specify which attributes are mass-assignable
    protected $fillable = [
        'fax_date',
        'fax_number',
        'reference_number',
        'remarks',
        'fax_document'
    ];

    protected $casts = [
        'fax_date' => 'datetime',
    ];

    protected $dates = ['deleted_at'];
}
