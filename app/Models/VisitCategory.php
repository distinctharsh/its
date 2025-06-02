<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisitCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name',
    ];

    public function category()
    {
        return $this->belongsTo(VisitCategory::class, 'category_id');
    }
}
