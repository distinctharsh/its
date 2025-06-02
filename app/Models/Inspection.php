<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class Inspection extends Model
{
    use HasFactory, SoftDeletes;

        protected $casts = [
            'deletion_date' => 'datetime', // Cast 'deletion_date' as a datetime
            'date_of_joining' => 'date',  // Cast 'date_of_joining' as a date
        ];
    
        // If you want to use Carbon for date handling, you can use $dates
        // In this case, both 'deletion_date' and 'updated_at' will be automatically converted to Carbon instances
        protected $dates = ['deletion_date', 'updated_at'];

    protected $fillable = [
        'inspector_id',
        'category_id', 
        'category_type_id',
        'date_of_joining',
   
      
        'remarks',
        'code',
        'deletion_date',
        'purpose_of_deletion',
        'objection_department_id',
        'routine_objection_document',
        'challenge_objection_document',
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

 
    public function departments()
    {
        return $this->belongsTo(Department::class, 'objection_department_id');  // status relationship
    }


}
