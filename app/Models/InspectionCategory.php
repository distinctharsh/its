<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name',
        'is_challenge',
        'inspection_types',
    ];

    protected $casts = [
        'inspection_types' => 'array',
    ];



    public function category()
    {
        return $this->belongsTo(InspectionCategory::class, 'category_id');
    }

    public function types()
    {
        return $this->belongsToMany(
            InspectionCategoryType::class,
            'inspection_category_inspection_category_type',
            'inspection_category_id',
            'inspection_category_type_id'
        )->select('inspection_category_types.id', 'inspection_category_types.type_name'); // Make sure 'type_name' is selected
    }
    

    
}
