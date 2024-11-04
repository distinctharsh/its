<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitSiteMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_code_id',
        'site_of_inspection',
        'visit_id',
        'state_id',
    ];

}
