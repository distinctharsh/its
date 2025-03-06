<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'inspector_id',
        'type_of_inspection_id',
        'site_of_inspection',
        'purpose_of_visit',
        'category_id',
        'point_of_entry',
        'point_of_exit',
        'clearance_certificate',
        'visit_report',
        'arrival_datetime',
        'list_of_inspectors',
        'list_of_escort_officers',
        'escort_officers_poe',
        'team_lead_id',
        'departure_datetime',
        'remarks',

        'inspection_category_id',  // Add this line
        'inspection_category_type_id',

        'site_code_id',
        'inspection_type_selection',

        'inspection_phase_id',
        'phase_option_id',
        'is_closed',
        'opcw_document_id',
        'inspection_property_id',
        'acentric_report',
        'to_the_points_comment',
     
 
    ];

    protected $casts = [
        'list_of_inspectors' => 'array',
        'list_of_escort_officers' => 'array',
        'escort_officers_poe' => 'array',
        'arrival_datetime' => 'datetime',
        'departure_datetime' => 'datetime',
       
    ];

    public function inspector()
    {
        return $this->belongsTo(Inspector::class, 'inspector_id')->withTrashed();
    }

    public function typeOfInspection()
    {
        return $this->belongsTo(InspectionType::class, 'type_of_inspection_id');
    }

    public function teamLead()
    {
        return $this->belongsTo(Inspector::class, 'team_lead_id');
    }
    
    public function inspectionType()
    {
        return $this->belongsTo(InspectionType::class, 'type_of_inspection_id');
    }

    public function category()
    {
        return $this->belongsTo(VisitCategory::class, 'category_id'); // Correct foreign key
    }

    public function escortOfficers()
    {
        return $this->belongsToMany(EscortOfficer::class, 'visit_escort_officer', 'visit_id', 'escort_officer_id');
    }

    public function inspectionCategory()
    {
        return $this->belongsTo(InspectionCategory::class, 'inspection_category_id')->withTrashed();
    }

    public function inspectionCategoryType()
    {
        return $this->belongsTo(InspectionCategoryType::class, 'inspection_category_type_id')->withTrashed();
    }


    public function siteMappings()
    {
        return $this->hasMany(VisitSiteMapping::class, 'visit_id');
    }


    public function inspectionPhase()
    {
        return $this->belongsTo(InspectionPhase::class, 'inspection_phase_id');
    }

    public function inspectionProperties()
    {
        return $this->belongsTo(InspectionProperties::class, 'inspection_property_id');
    }


    public function documentNumber()
    {
        return $this->belongsTo(OpcwFax::class, 'opcw_document_id');
    }

    public function inspectionIssue()
    {
        return $this->belongsTo(InspectionIssue::class, 'inspection_issue_id');
    }






    protected $dates = ['deleted_at'];
}
