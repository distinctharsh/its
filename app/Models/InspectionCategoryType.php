<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionCategoryType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['type_name'];

    public function categories()
    {
        return $this->belongsToMany(InspectionCategory::class, 'inspection_category_inspection_category_type');
    }
}
