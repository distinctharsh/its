<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionPhaseOption extends Model
{
    use HasFactory, SoftDeletes;


    protected $table = 'inspection_phase_options';

    protected $fillable = [
        'option_name',
    ];
}
