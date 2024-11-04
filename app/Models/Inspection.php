<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inspector_id',
        'category_id', 
        'category_type_id',
        'date_of_joining',
        'status_id',
      
        'remarks',
        'created_by',
    ];

    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id');
    }

    public function category()
    {
        return $this->belongsTo(InspectionCategory::class, 'category_id'); // Correct foreign key
    }

    public function categoryType() // Add relationship for category type
    {
        return $this->belongsTo(InspectionCategoryType::class, 'category_type_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');  // status relationship
    }

    protected $casts = [
        'date_of_joining' => 'date',
    ];
}
