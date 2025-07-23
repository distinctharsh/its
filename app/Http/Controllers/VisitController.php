<?php

namespace App\Http\Controllers;

use App\Models\EntryExitPoint;
use App\Models\InspectionPhase;
use App\Services\LoggingService;
use App\Models\EscortOfficer;
use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\InspectionCategoryType;
use App\Models\InspectionIssue;
use App\Models\InspectionPhaseOption;
use App\Models\InspectionProperties;
use App\Models\InspectionType;
use App\Models\Inspector;
use App\Models\OpcwFax;
use App\Models\PageLock;
use App\Models\SiteCode;
use App\Models\State;
use App\Models\Visit;
use App\Models\VisitCategory;
use App\Models\VisitSiteMapping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    //

    public function manageVisit()
    {

        $visits = Visit::withTrashed()
            ->with(['inspector', 'inspectionType', 'siteMappings', 'teamLead', 'inspectionCategoryType', 'inspectionProperties', 'inspectionIssue'])
            ->latest()
            ->get();

            
        $states = State::whereNull('deleted_at')->get();
        $entry_exit_points = EntryExitPoint::whereNull('deleted_at')->get();

        $site_codes = SiteCode::whereNull('deleted_at')->get();
        $inspection_phases = InspectionPhase::whereNull('deleted_at')->get();
        $visit_categories = VisitCategory::whereNull('deleted_at')->get();


         // 🔒 Check if there's an active lock for this page
        $visitLock = PageLock::where('page', 'inspection')
        ->where('locked', true)
        ->first();

        return view('manage-visit', compact('visits', 'site_codes', 'inspection_phases', 'states', 'visit_categories', 'entry_exit_points', 'visitLock'));
    }


    public function show($id)
    {
        $visit = Visit::with([
            'inspector',
            'typeOfInspection',
            'teamLead',
            'inspectionType',
            'siteMappings',
            'category',
            'inspectionCategory',
            'inspectionCategoryType'
        ])->withTrashed()->find($id);

        if (!$visit) {
            abort(404);
        }
        $states = State::whereNull('deleted_at')->get();
        $siteCodes = SiteCode::whereNull('deleted_at')->get();
        $inspectorIds = json_decode($visit->list_of_inspectors, true) ?? [];
        $inspectors = Inspector::whereIn('id', $inspectorIds)->pluck('name')->toArray();

        $escortOfficersIds = json_decode(trim($visit->list_of_escort_officers, '"'), true) ?? [];
        $escortOfficers = EscortOfficer::whereIn('id', $escortOfficersIds)->pluck('officer_name')->toArray();
        $entry_exit_points = EntryExitPoint::whereNull('deleted_at')->get();

         // 🔒 Check if there's an active lock for this page
        $visitLock = PageLock::where('page', 'inspection')
        ->where('locked', true)
        ->first();

        return view('visit-details', compact('visit', 'inspectors', 'escortOfficers', 'states', 'siteCodes', 'entry_exit_points', 'visitLock'));
    }




    public function addVisit()
    {
        $inspectors = Inspector::whereNull('deleted_at')->get();
        $visit_categories = VisitCategory::whereNull('deleted_at')->get();
        $inspection_types = InspectionType::whereNull('deleted_at')->get();
        $all_inspection_category = InspectionType::whereNull('deleted_at')->get();
        $escort_officers = EscortOfficer::whereNull('deleted_at')->get();
        $site_codes = SiteCode::whereNull('deleted_at')->get();
        $states = State::whereNull('deleted_at')->get();
        // Fetch inspection categories with their types
        $inspection_categories = InspectionCategory::with('types')->get();
        $inspection_phases = InspectionPhase::whereNull('deleted_at')->get();
        $phase_options = InspectionPhaseOption::whereNull('deleted_at')->get();
        $point_address = EntryExitPoint::whereNull('deleted_at')->get();
        $inspection_properties = InspectionProperties::whereNull('deleted_at')->get();
        $inspection_categories_type = InspectionCategoryType::whereNull('deleted_at')->get();
        $inspection_issues = InspectionIssue::whereNull('deleted_at')->get();


        $opcw_document_numbers = OpcwFax::whereNull('deleted_at')->pluck('fax_number', 'id');


        return view('add-visit', compact('inspectors', 'inspection_types', 'inspection_issues', 'all_inspection_category', 'inspection_categories_type', 'inspection_properties', 'opcw_document_numbers', 'inspection_phases', 'point_address', 'phase_options', 'visit_categories', 'escort_officers', 'inspection_categories', 'site_codes', 'states'));
    }

    public function createVisit(Request $request)
    {
        try {
            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');

            // if ($captchaInput !== $captchaSession) {
            //     return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            // }

            $validated = $request->validate([
                'escort_officers' => 'required|array',
                'escort_officers_poe' => 'required|array',
                'category_id' => 'required|exists:visit_categories,id',
                'team_lead' => 'required|integer',
                'inspection_issue_id' => 'nullable|array',
                'inspection_issue_id.*' => 'exists:inspection_issues,id|nullable',
                'list_of_inspectors' => 'required|array',
                'site_code_id' => 'required|array',
                'site_code_id.*' => 'exists:site_codes,id',
                'site_of_inspection' => 'required|array',
                'site_of_inspection.*' => 'string|max:255',
                'state_id' => 'required|array',
                'state_id.*' => 'exists:states,id',
                'arrival_datetime' => 'required|date',
                'departure_datetime' => 'required|date',
                'inspection_type_selection' => 'nullable|string',
                'category_type_id' => 'nullable|exists:inspection_category_types,id',
                'clearance_certificate' => 'nullable|file|mimes:pdf|max:51200',
                'visit_report' => 'nullable|file|mimes:pdf|max:10240',
                'remarks' => 'nullable|string',
                'acentric_report' => 'nullable|string',
                // 'to_the_points_comment' => 'nullable|string',
                'point_of_entry' => 'required|array', 
                'point_of_exit' => 'required|array', 
                'is_closed' => 'required|boolean', 
                'opcw_document_id' => 'required',
                'fax_document' => 'nullable|max:5120',
               
                
                'inspection_category_id' => 'required|array',
                'inspection_category_id.*' => 'exists:inspection_types,id',
                'inspection_phase_id' => 'required|array',
                'inspection_phase_id.*' => 'exists:inspection_phases,id',
                'phase_option_id' => 'nullable|array',
                'phase_option_id.*' => 'exists:inspection_phase_options,id',
                'preliminary_report' => 'nullable|array',
                'preliminary_report.*' => 'file|mimes:pdf|max:51200',
                'final_inspection_report' => 'nullable|array',
                'final_inspection_report.*' => 'file|mimes:pdf|max:51200',
                'issue_document' => 'nullable|array',
                'issue_document.*' => 'file|mimes:pdf|max:51200',
            ]);

            $inspectionTypeSelection = null;

            // If inspection_type_selection is 1, set 'routine'; if it's 2, set 'challenge'
            if ($validated['inspection_type_selection'] == 1) {
                $inspectionTypeSelection = 'routine';
            } elseif ($validated['inspection_type_selection'] == 2) {
                $inspectionTypeSelection = 'challenge';
            }

          
       
            $preliminaryReportPaths = [];
            if ($request->hasFile('preliminary_report')) {
                // Ensure the preliminary_report is always treated as an array (even if only one file is uploaded)
                $files = $request->file('preliminary_report');
                
                // If it's a single file, convert it into an array
                if (!is_array($files)) {
                    $files = [$files];
                }

                // Iterate over the files to store each file
                foreach ($files as $file) {
                    $preliminaryReportPaths[] = $file->store('visit_preliminary_reports');
                }
            }

            $finalInspectionReportPaths = [];
            if ($request->hasFile('final_inspection_report')) {
                // Ensure the final_inspection_report is always treated as an array (even if only one file is uploaded)
                $files = $request->file('final_inspection_report');
                
                // If it's a single file, convert it into an array
                if (!is_array($files)) {
                    $files = [$files];
                }

                // Iterate over the files to store each file
                foreach ($files as $file) {
                    $finalInspectionReportPaths[] = $file->store('visit_final_inspection_reports');
                }
            }


            $issueDocumentPath = [];
            if ($request->hasFile('issue_document')) {
                // Ensure the issue_document is always treated as an array (even if only one file is uploaded)
                $files = $request->file('issue_document');
                
                // If it's a single file, convert it into an array
                if (!is_array($files)) {
                    $files = [$files];
                }

                // Iterate over the files to store each file
                foreach ($files as $file) {
                    $issueDocumentPath[] = $file->store('visit_issue_documents');
                }
            }

            



            $visitReportPath = $request->hasFile('visit_report') ?
                $request->file('visit_report')->store('visit_reports') : null;

            $faxDocumentPath = null;
            if($request->fax_document != null){
                
                if ($request->hasFile('fax_document')) {
                    $faxDocumentPath = $request->file('fax_document')->store('opcw_fax_documents');
                }
            }

                
            // Insert visit data
            $visitData = [
                'list_of_escort_officers' => json_encode($validated['escort_officers']),
                'escort_officers_poe' => json_encode($validated['escort_officers_poe']),
                'type_of_inspection_id' => 1,
                'category_id' => $validated['category_id'],
                'team_lead_id' => $validated['team_lead'],
                'inspector_id' => $validated['team_lead'],
                'list_of_inspectors' => json_encode($validated['list_of_inspectors']),
                'point_of_entry' => json_encode($validated['point_of_entry']),
                'point_of_exit' => json_encode($validated['point_of_exit']),
               
                'arrival_datetime' => $validated['arrival_datetime'],
                'departure_datetime' => $validated['departure_datetime'],
                'inspection_category_type_id' => $validated['category_type_id'] ?? null,
                'purpose_of_visit' => 'Default Purpose',
               
                'site_of_inspection' => 'Default Site',
                // 'clearance_certificate' => $clearanceCertificatePath,
                'visit_report' => $visitReportPath,
             
                'remarks' => $validated['remarks'],
                'acentric_report' => $validated['acentric_report'],
                // 'to_the_points_comment' => $validated['to_the_points_comment'],
                'inspection_type_selection' => $inspectionTypeSelection,
                'inspection_property_id' => $validated['inspection_type_selection'],
                'inspection_phase_id' => 1,
                'is_closed' => $validated['is_closed'],
                'opcw_document_id' => $validated['opcw_document_id'],

                'is_draft' => strtolower(auth()->user()->role->name) === 'user',
                'is_reverted' => 0,
                
            ];
            // Create the visit record
            $visit = Visit::create($visitData);

            // Log activity
            // $recordId = $visit->id;
            // $changes = ['action' => 'New Visit added'];
            // LoggingService::logActivity($request, 'insert', 'visits', $recordId, $changes);



             // Enhanced logging for visit creation
            $recordId = $visit->id;
            $changes = [
                'action' => 'New Inspection created by ' . auth()->user()->name,
                'details' => [
                    'visit_id' => $visit->id,
                    'category' => $visit->category->name ?? null,
                    'Inspector (Team Lead)' => $visit->teamLead->name ?? null,
                    'Arrival Date & Time' => $visit->arrival_datetime,
                    'Departure Date & Time' => $visit->departure_datetime,
                    'Point of Entry' => json_decode($visit->point_of_entry, true),
                    'Point of Exit' => json_decode($visit->point_of_exit, true),
                    'is_closed' => $visit->is_closed ? 'Yes' : 'No',
                    
                    'files_uploaded' => [
                        'visit_report' => $visit->visit_report ? 'Yes' : 'No',
                        'fax_document' => $faxDocumentPath ? 'Yes' : 'No'
                    ]
                ]
            ];
            LoggingService::logActivity($request, 'insert', 'inspections', $recordId, $changes);

            if (!empty($faxDocumentPath)) {
                $opcwFax = OpcwFax::find($request->opcw_document_id);  
                if ($opcwFax) { 
                    $opcwFax->fax_document = $faxDocumentPath;
                    $opcwFax->save();
                } else {
                    Log::error('OpcwFax record not found for ID: ' . $request->opcw_document_id);
                }
            } else {
                Log::info('No fax document uploaded or faxDocumentPath is null, skipping update.');
            }

            // Attach escort officers to the visit in the pivot table
            $escortOfficerIds = $validated['escort_officers'];
            $visit->escortOfficers()->sync($escortOfficerIds); 

           

            // Create site mappings for the visit
            $siteCodes = $request->input('site_code_id');
            $inspectionCategoryId = $request->input('inspection_category_id');
            $inspectionPhaseId = $request->input('inspection_phase_id');
            $sitesOfInspection = $request->input('site_of_inspection');
            $stateIds = $request->input('state_id');
            $phaseOptionId = $request->input('phase_option_id');
            $inspectionIssueId = $validated['inspection_issue_id']; // New field

           
            for ($i = 0; $i < count($siteCodes); $i++) {
                $phaseOption = isset($phaseOptionId[$i]) ? $phaseOptionId[$i] : null;

                $visitSiteMapping = VisitSiteMapping::create([
                    'site_code_id' => $siteCodes[$i],
                    'inspection_category_id' => $inspectionCategoryId[$i],
                    'inspection_phase_id' => $inspectionPhaseId[$i],
                    'site_of_inspection' => $sitesOfInspection[$i],
                    'phase_option_id' => $phaseOption,
                    'visit_id' => $visit->id,
                   
                    'inspection_issue_id' => isset($inspectionIssueId[$i]) ? $inspectionIssueId[$i] : null,  // Handle per iteration
                    'issue_document' => isset($issueDocumentPath[$i]) ? $issueDocumentPath[$i] : null, 
                    'preliminary_report' => $preliminaryReportPaths[$i] ?? null,
                    'final_inspection_report' => $finalInspectionReportPaths[$i] ?? null,
                    'state_id' => $stateIds[$i],
                ]);

                $recordId = $visitSiteMapping->id;
                $changes = ['action' => 'New Visit Site Mapping added'];
                LoggingService::logActivity($request, 'insert', 'visit_site_mappings', $recordId, $changes);
            }

            return response()->json(['success' => true, 'msg' => 'Inspection added successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation errors: ', $e->validator->errors()->all());
            return response()->json(['success' => false, 'msg' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        }
    }


    public function editVisit($id)
    {
        try {
            $visit = Visit::withTrashed()->findOrFail($id);
            $inspectors = Inspector::whereNull('deleted_at')->get();
            $visit_categories = VisitCategory::whereNull('deleted_at')->get();
            $inspection_types = InspectionType::whereNull('deleted_at')->get();
            $escort_officers = EscortOfficer::whereNull('deleted_at')->get();
            $site_codes = SiteCode::whereNull('deleted_at')->get();
            $states = State::whereNull('deleted_at')->get();
            // Fetch inspection categories with their types
            $inspection_categories = InspectionCategory::with('types')->get();
            $inspection_phases = InspectionPhase::whereNull('deleted_at')->get();
            $phase_options = InspectionPhaseOption::whereNull('deleted_at')->get();
            $visit_site_mapping = VisitSiteMapping::where('visit_id', $id)->get();

            $point_address = EntryExitPoint::whereNull('deleted_at')->get();
            $opcw_document_numbers = OpcwFax::whereNull('deleted_at')->pluck('fax_number', 'id');

            $inspection_properties = InspectionProperties::whereNull('deleted_at')->get();
            $inspection_categories_type = InspectionCategoryType::whereNull('deleted_at')->get();
            $all_inspection_category = InspectionType::whereNull('deleted_at')->get();
            $inspection_issues = InspectionIssue::whereNull('deleted_at')->get();
            return view('edit-visit', compact('inspectors', 'phase_options', 'inspection_issues', 'all_inspection_category', 'inspection_categories_type', 'inspection_properties', 'point_address', 'opcw_document_numbers', 'inspection_phases', 'visit',  'inspection_types', 'visit_site_mapping', 'visit_categories', 'escort_officers', 'inspection_categories', 'site_codes', 'states'));
        } catch (\Exception $e) {
            \Log::error('Edit Visit Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'msg' => 'An error occurred while retrieving the visit.'
            ], 500);
        }
    }

    // Method to update an existing visit
    public function updateVisit(Request $request, $id)
    {
        try {
            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');

            // if ($captchaInput !== $captchaSession) {
            //     return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            // }

            // Validate incoming request
            $validated = $request->validate([
                'escort_officers' => 'required|array',
                'category_id' => 'required|exists:visit_categories,id',
                'team_lead' => 'required|integer',
              
                'list_of_inspectors' => 'required|array',
                'site_code_id' => 'required|array',
                'site_code_id.*' => 'exists:site_codes,id',
                'site_of_inspection' => 'required|array',
                'site_of_inspection.*' => 'string|max:255',
                'state_id' => 'required|array',
                'state_id.*' => 'exists:states,id',
                'arrival_datetime' => 'required|date',
                'departure_datetime' => 'required|date',
                'inspection_type_selection' => 'nullable|string',
                'category_type_id' => 'nullable|exists:inspection_category_types,id',
                'clearance_certificate' => 'nullable|file|mimes:pdf|max:51200',
                'visit_report' => 'nullable|file|mimes:pdf|max:10240',
                'remarks' => 'nullable|string',
                'acentric_report' => 'nullable|string',
                // 'to_the_points_comment' => 'nullable|string',
                'point_of_entry' => 'required|array', 
                'point_of_exit' => 'required|array',
                'is_closed' => 'required|boolean',

                'opcw_document_id' => 'required',
                'fax_document' => 'nullable|max:5120',


                'inspection_category_id' => 'required|array',
                'inspection_category_id.*' => 'exists:inspection_types,id',

                'inspection_issue_id' => 'nullable|array',
                'inspection_issue_id.*' => 'exists:inspection_issues,id|nullable',

                'inspection_phase_id' => 'required|array',
                'inspection_phase_id.*' => 'exists:inspection_phases,id',

                'phase_option_id' => 'nullable|array',
                'phase_option_id.*' => 'exists:inspection_phase_options,id',

                'preliminary_report' => 'nullable|array',
                'preliminary_report.*' => 'file|mimes:pdf|max:51200',

                'final_inspection_report' => 'nullable|array',
                'final_inspection_report.*' => 'file|mimes:pdf|max:51200',

                'escort_officers_poe' => 'required|array',

                'issue_document' => 'nullable|max:5120',
            ]);
           
            $visit = Visit::withTrashed()->findOrFail($id);
            $visitSiteMappingData = VisitSiteMapping::where('visit_id', $visit->id)->get();

            // dd($visitSiteMappingData->toArray());

            $visit = Visit::withTrashed()->find($id);
            if (!$visit) {
                return response()->json(['success' => false, 'msg' => 'Visit record not found.'], 404);
            }

            $inspectionTypeSelection = null;

             // If inspection_type_selection is 1, set 'routine'; if it's 2, set 'challenge'
             if ($validated['inspection_type_selection'] == 1) {
                $inspectionTypeSelection = 'routine';
            } elseif ($validated['inspection_type_selection'] == 2) {
                $inspectionTypeSelection = 'challenge';
            }

            $faxDocumentPath = null;

            if ($request->has('fax_document') && !empty($request->fax_document)) {
                if ($request->hasFile('fax_document')) {
                    $faxDocumentPath = $request->file('fax_document')->store('opcw_fax_documents');
                }
            }

            // Capture the original values for logging
            $originalData = [
                'inspector_id' => $visit->inspector_id,
                'type_of_inspection_id' => $visit->type_of_inspection_id,
                'category_id' => $visit->category_id,
                'site_of_inspection' => json_decode($visit->site_of_inspection),
                'arrival_datetime' => $visit->arrival_datetime,
                'departure_datetime' => $visit->departure_datetime,
                'list_of_inspectors' => json_decode($visit->list_of_inspectors),
                'point_of_entry' => json_decode($visit->point_of_entry),
                'point_of_exit' => json_decode($visit->point_of_exit),
                'list_of_escort_officers' => json_decode($visit->list_of_escort_officers),
                'clearance_certificate' => $visit->clearance_certificate,
                'visit_report' => $visit->visit_report,
                'is_closed' => $visit->is_closed,
                'opcw_document_id' => $visit->opcw_document_id,
                'inspection_property_id' => $visit->inspection_type_selection,
                'inspection_type_selection' => $inspectionTypeSelection,

                'escort_officers_poe' => json_decode($visit->escort_officers_poe),
                
                'remarks' => $visit->remarks,
                'acentric_report' => $visit->acentric_report,
                // 'to_the_points_comment' => $visit->to_the_points_comment,
            ];

            // Handle file uploads
            $validated['clearance_certificate'] = $request->hasFile('clearance_certificate')
                ? $request->file('clearance_certificate')->store('visit_clearance_certificates')
                : $visit->clearance_certificate;

            $validated['visit_report'] = $request->hasFile('visit_report')
                ? $request->file('visit_report')->store('visit_reports')
                : $visit->visit_report;



                $preliminaryReportPaths = [];
                $finalInspectionReportPaths = [];
                $issueDocumentPaths = [];
                
                // Handle Preliminary Reports
                if ($request->hasFile('preliminary_report')) {
                    foreach ($request->file('preliminary_report') as $index => $file) {
                        if ($file->isValid()) {
                            $preliminaryReportPaths[$index] = $file->store('reports');
                        }
                    }
                }
                
                // Handle Final Inspection Reports
                if ($request->hasFile('final_inspection_report')) {
                
                    foreach ($request->file('final_inspection_report') as $index => $file) {
                        if ($file->isValid()) {
                            $finalInspectionReportPaths[$index] = $file->store('reports');
                        }
                    }
                }
                
                // Handle Issue Documents
                if ($request->hasFile('issue_document')) {
                    $files = $request->file('issue_document');
                
                    // Check if multiple files or single file
                    $files = is_array($files) ? $files : [$files];
                
                    foreach ($files as $file) {
                        if ($file->isValid()) {
                            $issueDocumentPaths[] = $file->store('visit_issue_documents');
                        }
                    }
                }

                  // Add conditional update fields
                $validated['is_draft'] = strtolower(auth()->user()->role->name) === 'user';
                $validated['is_reverted'] = 0;
                
            // Update visit record
            $visit->update([
                'inspector_id' => $validated['team_lead'],
                
                'inspection_category_type_id' => $validated['category_type_id'],
           
                'category_id' => $validated['category_id'],
                'site_of_inspection' => json_encode($validated['site_of_inspection']),
                'arrival_datetime' => $validated['arrival_datetime'],
                'departure_datetime' => $validated['departure_datetime'],
                'list_of_inspectors' => json_encode($validated['list_of_inspectors']),
                'point_of_exit' => json_encode($validated['point_of_exit']),
                'point_of_entry' => json_encode($validated['point_of_entry']),
                'list_of_escort_officers' => json_encode($validated['escort_officers']),
                'team_lead_id' => $validated['team_lead'],
                'clearance_certificate' => $validated['clearance_certificate'],
                'visit_report' => $validated['visit_report'],
                'is_closed' => $validated['is_closed'],
                'remarks' => $validated['remarks'],
                'acentric_report' => $validated['acentric_report'],
                // 'to_the_points_comment' => $validated['to_the_points_comment'],

                'escort_officers_poe' => json_encode($validated['escort_officers_poe']),
                'issue_document' => $issueDocumentPaths,

                'inspection_property_id' => $validated['inspection_type_selection'],
                'inspection_type_selection' => $inspectionTypeSelection,
                'opcw_document_id' => $validated['opcw_document_id'],

                'is_draft' => $validated['is_draft'],
                'is_reverted' => $validated['is_reverted'],

                 
            ]);


            // Log the changes
            $changes = [
                'old_data' => $originalData,
                'new_data' => [
                    'inspector_id' => $validated['team_lead'],
            
                    'category_id' => $validated['category_id'],
                    'site_of_inspection' => $validated['site_of_inspection'],
                    'arrival_datetime' => $validated['arrival_datetime'],
                    'departure_datetime' => $validated['departure_datetime'],
                    'list_of_inspectors' => $validated['list_of_inspectors'],
                    'point_of_entry' => $validated['point_of_entry'],
                    'point_of_exit' => $validated['point_of_exit'],
                    'list_of_escort_officers' => $validated['escort_officers'],
                    'clearance_certificate' => $validated['clearance_certificate'],
                    'visit_report' => $validated['visit_report'],
                    'is_closed' => $validated['is_closed'],
                    'opcw_document_id' => $validated['opcw_document_id'],
                    'inspection_property_id' => $validated['inspection_type_selection'],
                    'inspection_type_selection' => $inspectionTypeSelection,
                    'remarks' => $validated['remarks'],
                    'acentric_report' => $validated['acentric_report'],
                    // 'to_the_points_comment' => $validated['to_the_points_comment'],
                    'escort_officers_poe' => $validated['escort_officers_poe'],
                ],
            ];
            LoggingService::logActivity($request, 'update', 'visits', $visit->id, $changes);

            if (!empty($faxDocumentPath)) {
                $opcwFax = OpcwFax::find($request->opcw_document_id);
            
                if ($opcwFax) {  
                    $opcwFax->fax_document = $faxDocumentPath; 
                    $opcwFax->save();
                } else {
                    Log::error('OpcwFax record not found for ID: ' . $request->opcw_document_id);
                }
            } else {
                Log::info('No fax document uploaded or faxDocumentPath is null, skipping update.');
            }

            // Insert new site mappings
            $siteCodes = $validated['site_code_id'];
            $sitesOfInspection = $validated['site_of_inspection'];
            $stateIds = $request->input('state_id');
            $inspectionCategoryId = $request->input('inspection_category_id');
            $inspectionIssueId = $validated['inspection_issue_id'];
            $inspectionPhaseId = $request->input('inspection_phase_id');
            $phaseOptionId = $request->input('phase_option_id');

            // Process each site mapping
            for ($i = 0; $i < count($siteCodes); $i++) {
                $phaseOption = isset($phaseOptionId[$i]) ? $phaseOptionId[$i] : null;

                // Retrieve the existing mapping, if any
                $existingMapping = VisitSiteMapping::where('visit_id', $visit->id)
                    ->where('site_code_id', $siteCodes[$i])
                    ->first();

                // Prepare data for VisitSiteMapping creation or update
                $siteMappingData = [
                    'site_code_id' => $siteCodes[$i],
                    'inspection_category_id' => $inspectionCategoryId[$i],
                    'inspection_issue_id' => $inspectionIssueId[$i],
                    'inspection_phase_id' => $inspectionPhaseId[$i],
                    'phase_option_id' => $phaseOption,
                    'site_of_inspection' => $sitesOfInspection[$i],
                    'visit_id' => $visit->id,
                    'state_id' => $stateIds[$i],
                ];

               
                // Only include files if they exist
                if (!empty($issueDocumentPaths)) {
                    $siteMappingData['issue_document'] = json_encode($issueDocumentPaths);
                }

                if (isset($preliminaryReportPaths[$i]) && !empty($preliminaryReportPaths[$i])) {
                    $siteMappingData['preliminary_report'] = $preliminaryReportPaths[$i];
                }

                if (isset($finalInspectionReportPaths[$i]) && !empty($finalInspectionReportPaths[$i])) {
                   
                    $siteMappingData['final_inspection_report'] = $finalInspectionReportPaths[$i];
                }
                
                if ($existingMapping) {
                    // Update the existing mapping
                    $existingMapping->update($siteMappingData);
                    $recordId = $existingMapping->id;
                    $changes = ['action' => 'Visit Site Mapping updated'];
                    LoggingService::logActivity($request, 'update', 'visit_site_mappings', $recordId, $changes);
                } else {
                    // Create a new mapping
                    $visitSiteMapping = VisitSiteMapping::create($siteMappingData);
                    $recordId = $visitSiteMapping->id;
                    $changes = ['action' => 'New Visit Site Mapping added'];
                    LoggingService::logActivity($request, 'insert', 'visit_site_mappings', $recordId, $changes);
                }
            }

            return response()->json(['success' => true, 'msg' => 'Inspection updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating visit: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'error_code' => $e->getCode(),
            ]);
            return response()->json(['success' => false, 'msg' => 'Failed to update inspection.', 'error_details' => $e->getMessage()]);
        }
    }





    public function deleteVisit(Request $request)
    {
        try {
            $visit = Visit::withTrashed()->findOrFail($request->visit_id);

            // Only delete if it's not already trashed
            if (!$visit->trashed()) {
                $visit->delete();

                $changes = [
                    'action' => 'Visit soft deleted'
                ];
                LoggingService::logActivity($request, 'delete', 'visits', $visit->id, $changes);

                return response()->json([
                    'success' => true,
                    'msg' => 'Visit deleted successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'Visit is already deleted!'
                ], 422);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

   public function updateVisitStatus(Request $request, $id)
{
    try {
        $visit = Visit::withTrashed()->findOrFail($id);
        $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

        if ($isActive) {
            if ($visit->trashed()) {
                $originalStatus = 'Deleted';
                $newStatus = 'Active';
                $visit->restore();
                $visit->save();

                // Log activity for restoring the visit
                $changes = [
                    'action' => 'Inspection Restored by ' . auth()->user()->name,
                    'details' => [
                        'visit_id' => $visit->id,
                        'team_lead' => $visit->teamLead->name ?? 'N/A',
                        'inspection_type' => $visit->inspectionType->name ?? 'N/A',
                        'previous_status' => $originalStatus,
                        'new_status' => $newStatus,
                        'restored_by' => auth()->user()->name,
                        'restored_at' => now()->format('Y-m-d H:i:s'),
                        'arrival_date' => $visit->arrival_datetime,
                        'departure_date' => $visit->departure_datetime,
                        'sites_count' => $visit->siteMappings->count()
                    ]
                ];
                LoggingService::logActivity($request, 'restore', 'visits', $visit->id, $changes);
            }
        } else {
            $originalStatus = 'Active';
            $newStatus = 'Deleted';
            
            // Log activity before deleting
            $changes = [
                'action' => 'Inspection Deleted by ' . auth()->user()->name,
                'details' => [
                    'visit_id' => $visit->id,
                    'team_lead' => $visit->teamLead->name ?? 'N/A',
                    'inspection_type' => $visit->inspectionType->name ?? 'N/A',
                    'previous_status' => $originalStatus,
                    'new_status' => $newStatus,
                    'deleted_by' => auth()->user()->name,
                    'deleted_at' => now()->format('Y-m-d H:i:s'),
                    'arrival_date' => $visit->arrival_datetime,
                    'departure_date' => $visit->departure_datetime,
                    'sites_count' => $visit->siteMappings->count()
                ]
            ];
            LoggingService::logActivity($request, 'delete', 'visits', $visit->id, $changes);

            // Soft delete the visit if it's not already deleted
            if (!$visit->trashed()) {
                $visit->delete();
            }
        }

        return response()->json([
            'success' => true,
            'msg' => 'Status updated successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'msg' => $e->getMessage()
        ], 500);
    }
}



    public function getFaxDetails(Request $request){
        $documentNumberId = $request->input('document_number_id'); // Get the ID from the AJAX request

    // Optionally, fetch data from the database based on the document number ID
    $faxDetails = OpcwFax::find($documentNumberId);

    // Return data as JSON response
    return response()->json($faxDetails);
    }



   public function approve($id)
    {
        $visit = Visit::findOrFail($id);

        if (auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin' && $visit->is_draft) {
            $originalData = $visit->only(['is_draft', 'is_reverted', 'reverted_at']);
            
            $visit->update([
                'is_draft' => false,
                'is_reverted' => false,
                'reverted_at' => null
            ]);

            // Log the approval
            $changes = [
                'action' => 'Inspection approved by ' . auth()->user()->name,
                'details' => [
                    'visit_id' => $visit->id,
                    'visit_reference' => $visit->reference_number ?? 'N/A',
                    'previous_status' => 'Draft',
                    'new_status' => 'Approved',
                    'changes' => [
                        'is_draft' => ['from' => true, 'to' => false],
                        'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => false],
                        'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => null]
                    ],
                    'team_lead' => $visit->teamLead->name ?? 'N/A',
                    'inspectors' => json_decode($visit->list_of_inspectors, true) ?? []
                ]
            ];
            
            LoggingService::logActivity(request(), 'Approved', 'visits', $visit->id, $changes);

            return redirect()->back()->with('success', 'Inspection approved successfully.');
        }

        return redirect()->back()->with('error', 'Unauthorized action.');
    }

        
    
    public function revert($id)
    {
        $visit = Visit::findOrFail($id);
        $originalData = $visit->only(['is_reverted', 'reverted_at', 'is_draft']);
        
        $visit->update([
            'is_reverted' => true,
            'reverted_at' => now(),
            'is_draft' => 0
        ]);

        // Log the revert action
        $changes = [
            'action' => 'Visit reverted by ' . auth()->user()->name,
            'details' => [
                'visit_id' => $visit->id,
                'visit_reference' => $visit->reference_number ?? 'N/A',
                'previous_status' => $originalData['is_draft'] ? 'Draft' : 'Approved',
                'new_status' => 'Reverted',
                'changes' => [
                    'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => true],
                    'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => now()],
                    'is_draft' => ['from' => $originalData['is_draft'], 'to' => false]
                ],
                'revert_reason' => request()->input('revert_reason', 'Not specified'),
                'team_lead' => $visit->teamLead->name ?? 'N/A'
            ]
        ];
        
        LoggingService::logActivity(request(), 'Reverted', 'visits', $visit->id, $changes);

        return redirect()->back()->with('success', 'Inspection marked as reverted successfully.');
    }

    public function sendToDraft($id)
    {
        $visit = Visit::findOrFail($id);
        $originalData = $visit->only(['is_draft', 'is_reverted', 'reverted_at']);
        
        $visit->update([
            'is_draft' => true,
            'is_reverted' => false,
            'reverted_at' => null,
        ]);

        // Log the move to draft action
        $changes = [
            'action' => 'Visit moved to draft by ' . auth()->user()->name,
            'details' => [
                'inspection_id' => $visit->id,
                'visit_reference' => $visit->reference_number ?? 'N/A',
                'previous_status' => $originalData['is_reverted'] ? 'Reverted' : 'Approved',
                'new_status' => 'Draft',
                'changes' => [
                    'is_draft' => ['from' => $originalData['is_draft'], 'to' => true],
                    'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => false],
                    'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => null]
                ],
                'team_lead' => $visit->teamLead->name ?? 'N/A',
                'draft_reason' => request()->input('draft_reason', 'Not specified')
            ]
        ];
        
        LoggingService::logActivity(request(), 'Pending for Approval', 'inspection', $visit->id, $changes);

        return redirect()->back()->with('success', 'Inspection moved to draft successfully.');
    }

    public function bulkApprove(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No visits selected.'
                ], 422);
            }
            $updated = 0;
            foreach ($ids as $id) {
                $visit = Visit::find($id);
                if ($visit && $visit->is_draft) {
                    $originalData = $visit->only(['is_draft', 'is_reverted', 'reverted_at']);
                    $visit->update([
                        'is_draft' => false,
                        'is_reverted' => false,
                        'reverted_at' => null
                    ]);
                    $changes = [
                        'action' => 'Visit approved by ' . auth()->user()->name . ' (bulk)',
                        'details' => [
                            'visit_id' => $visit->id,
                            'previous_status' => 'Draft',
                            'new_status' => 'Approved',
                            'changes' => [
                                'is_draft' => ['from' => true, 'to' => false],
                                'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => false],
                                'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => null]
                            ]
                        ]
                    ];
                    LoggingService::logActivity($request, 'Approved', 'visits', $visit->id, $changes);
                    $updated++;
                }
            }
            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'msg' => "$updated visit(s) approved successfully."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'No visits were approved.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkReject(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No visits selected.'
                ], 422);
            }
            $updated = 0;
            foreach ($ids as $id) {
                $visit = Visit::find($id);
                if ($visit && $visit->is_draft) {
                    $originalData = $visit->only(['is_reverted', 'reverted_at', 'is_draft']);
                    $visit->update([
                        'is_reverted' => true,
                        'reverted_at' => now(),
                        'is_draft' => 0
                    ]);
                    $changes = [
                        'action' => 'Visit rejected by ' . auth()->user()->name . ' (bulk)',
                        'details' => [
                            'visit_id' => $visit->id,
                            'previous_status' => 'Draft',
                            'new_status' => 'Rejected',
                            'changes' => [
                                'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => true],
                                'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => now()],
                                'is_draft' => ['from' => $originalData['is_draft'], 'to' => false]
                            ]
                        ]
                    ];
                    LoggingService::logActivity($request, 'Rejected', 'visits', $visit->id, $changes);
                    $updated++;
                }
            }
            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'msg' => "$updated visit(s) rejected successfully."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'No visits were rejected.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkRevert(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            if (!is_array($ids) || empty($ids)) {
                return response()->json([
                    'success' => false,
                    'msg' => 'No visits selected.'
                ], 422);
            }
            $updated = 0;
            foreach ($ids as $id) {
                $visit = Visit::find($id);
                if ($visit && !$visit->is_reverted) {
                    $originalData = $visit->only(['is_reverted', 'reverted_at', 'is_draft']);
                    $visit->update([
                        'is_reverted' => true,
                        'reverted_at' => now(),
                        'is_draft' => 0
                    ]);
                    $changes = [
                        'action' => 'Visit reverted by ' . auth()->user()->name . ' (bulk)',
                        'details' => [
                            'visit_id' => $visit->id,
                            'previous_status' => $originalData['is_draft'] ? 'Draft' : 'Approved',
                            'new_status' => 'Reverted',
                            'changes' => [
                                'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => true],
                                'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => now()],
                                'is_draft' => ['from' => $originalData['is_draft'], 'to' => false]
                            ]
                        ]
                    ];
                    LoggingService::logActivity($request, 'Reverted', 'visits', $visit->id, $changes);
                    $updated++;
                }
            }
            if ($updated > 0) {
                return response()->json([
                    'success' => true,
                    'msg' => "$updated visit(s) reverted successfully."
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'No visits were reverted.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }


}
