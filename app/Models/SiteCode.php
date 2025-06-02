<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteCode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'site_code',
        'site_name',
        'site_address',
        'state_id',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
