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
        'inspection_category_id',
        'inspection_phase_id',
        'phase_option_id',
        'preliminary_report',
        'final_inspection_report',
        'inspection_issue_id',
        'issue_document'
    ];

    public function inspectionType()
    {
        return $this->belongsTo(InspectionType::class, 'inspection_category_id');
    }


    public function inspectionIssue()
    {
        return $this->belongsTo(InspectionIssue::class, 'inspection_issue_id');
    }


}
