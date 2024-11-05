<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\EscortOfficer;
use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\InspectionCategoryType;
use App\Models\InspectionType;
use App\Models\Inspector;
use App\Models\SiteCode;
use App\Models\State;
use App\Models\Visit;
use App\Models\VisitCategory;
use App\Models\VisitSiteMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VisitController extends Controller
{
    //

    public function manageVisit()
    {

        $visits = Visit::withTrashed()
            ->with(['inspector', 'inspectionType', 'siteMappings', 'teamLead', 'inspectionCategoryType'])
            ->get();
        $states = State::whereNull('deleted_at')->get();
      
        $site_codes = SiteCode::whereNull('deleted_at')->get();

        return view('manage-visit', compact('visits', 'site_codes', 'states'));
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
        return view('visit-details', compact('visit', 'inspectors', 'escortOfficers', 'states', 'siteCodes'));
    }




    public function addVisit()
    {
        $inspectors = Inspector::whereNull('deleted_at')->get();
        $visit_categories = VisitCategory::whereNull('deleted_at')->get();
        $inspection_types = InspectionType::whereNull('deleted_at')->get();
        $escort_officers = EscortOfficer::whereNull('deleted_at')->get();
        $site_codes = SiteCode::whereNull('deleted_at')->get();
        $states = State::whereNull('deleted_at')->get();
        // Fetch inspection categories with their types
        $inspection_categories = InspectionCategory::with('types')->get();


        return view('add-visit', compact('inspectors', 'inspection_types', 'visit_categories', 'escort_officers', 'inspection_categories', 'site_codes', 'states'));
    }

    public function createVisit(Request $request)
    {
        $data = $request->all();
        Log::info('Create Visit request received', $data);

        try {
            $captchaInput = $request->input('captcha');
            $captchaSession = $request->session()->get('captcha_text');

            if ($captchaInput !== $captchaSession) {
                return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            }

            $validated = $request->validate([
                'escort_officers' => 'required|array',
                'inspection_type' => 'required|integer',
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
            ]);


            $clearanceCertificatePath = $request->hasFile('clearance_certificate') ?
                $request->file('clearance_certificate')->store('visit_clearance_certificate') : null;

            $visitReportPath = $request->hasFile('visit_report') ?
                $request->file('visit_report')->store('visit_reports') : null;

            $visitData = [
                'list_of_escort_officers' => json_encode($validated['escort_officers']),
                'type_of_inspection_id' => $validated['inspection_type'],
                'category_id' => $validated['category_id'],
                'team_lead_id' => $validated['team_lead'],
                'inspector_id' => $validated['team_lead'],
                'list_of_inspectors' => json_encode($validated['list_of_inspectors']),
                'arrival_datetime' => $validated['arrival_datetime'],
                'departure_datetime' => $validated['departure_datetime'],
                'inspection_category_type_id' => $validated['category_type_id'] ?? null,
                'purpose_of_visit' => 'Default Purpose',
                'point_of_entry' => 'Site Visit',
                'site_of_inspection' => 'Default Site',
                'clearance_certificate' => $clearanceCertificatePath,
                'visit_report' => $visitReportPath,
                'remarks' => $validated['remarks'],
                'inspection_type_selection' => $validated['inspection_type_selection'],
            ];

            $visit = Visit::create($visitData);

            $recordId = $visit->id;
            $changes = ['action' =>'New Visit added'];
            LoggingService::logActivity($request, 'insert', 'visits', $recordId, $changes);

            $siteCodes = $request->input('site_code_id');
            $sitesOfInspection = $request->input('site_of_inspection');
            $stateIds = $request->input('state_id');

            for ($i = 0; $i < count($siteCodes); $i++) {
                $visitSiteMapping = VisitSiteMapping::create([
                    'site_code_id' => $siteCodes[$i],
                    'site_of_inspection' => $sitesOfInspection[$i],
                    'visit_id' => $visit->id,
                    'state_id' => $stateIds[$i],
                ]);

                $recordId = $visitSiteMapping->id;
                $changes = ['action' =>'New Visit Site Mapping added'];
                LoggingService::logActivity($request, 'insert', 'visit_site_mappings', $recordId, $changes);
            }

            return response()->json(['success' => true, 'msg' => 'Visit(s) added successfully.']);
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

            $visit_site_mapping = VisitSiteMapping::where('visit_id', $id)->get();


            return view('edit-visit', compact('inspectors', 'visit',  'inspection_types', 'visit_site_mapping', 'visit_categories', 'escort_officers', 'inspection_categories', 'site_codes', 'states'));
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
            // Validate CAPTCHA
            $captchaInput = $request->input('captcha');
            $captchaSession = $request->session()->get('captcha_text');

            if ($captchaInput !== $captchaSession) {
                return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            }

            // Validate incoming request
            $validated = $request->validate([
                'escort_officers' => 'required|array',
                'inspection_type' => 'required|integer',
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
            ]);

            $visit = Visit::withTrashed()->findOrFail($id);

            // Capture the original values for logging
            $originalData = [
                'inspector_id' => $visit->inspector_id,
                'type_of_inspection_id' => $visit->type_of_inspection_id,
                'category_id' => $visit->category_id,
                'site_of_inspection' => json_decode($visit->site_of_inspection),
                'arrival_datetime' => $visit->arrival_datetime,
                'departure_datetime' => $visit->departure_datetime,
                'list_of_inspectors' => json_decode($visit->list_of_inspectors),
                'list_of_escort_officers' => json_decode($visit->list_of_escort_officers),
                'clearance_certificate' => $visit->clearance_certificate,
                'visit_report' => $visit->visit_report,
                'remarks' => $visit->remarks,
            ];

            // Handle file uploads
            $validated['clearance_certificate'] = $request->hasFile('clearance_certificate')
                ? $request->file('clearance_certificate')->store('visit_clearance_certificates')
                : $visit->clearance_certificate;

            $validated['visit_report'] = $request->hasFile('visit_report')
                ? $request->file('visit_report')->store('visit_reports')
                : $visit->visit_report;

            // Update visit record
            $visit->update([
                'inspector_id' => $validated['team_lead'],
                'type_of_inspection_id' => $validated['inspection_type'],
                'category_id' => $validated['category_id'],
                'site_of_inspection' => json_encode($validated['site_of_inspection']),
                'arrival_datetime' => $validated['arrival_datetime'],
                'departure_datetime' => $validated['departure_datetime'],
                'list_of_inspectors' => json_encode($validated['list_of_inspectors']),
                'list_of_escort_officers' => json_encode($validated['escort_officers']),
                'team_lead_id' => $validated['team_lead'],
                'clearance_certificate' => $validated['clearance_certificate'],
                'visit_report' => $validated['visit_report'],
                'remarks' => $validated['remarks'],
            ]);


            // Log the changes
            $changes = [
                'old_data' => $originalData,
                'new_data' => [
                    'inspector_id' => $validated['team_lead'],
                    'type_of_inspection_id' => $validated['inspection_type'],
                    'category_id' => $validated['category_id'],
                    'site_of_inspection' => $validated['site_of_inspection'],
                    'arrival_datetime' => $validated['arrival_datetime'],
                    'departure_datetime' => $validated['departure_datetime'],
                    'list_of_inspectors' => $validated['list_of_inspectors'],
                    'list_of_escort_officers' => $validated['escort_officers'],
                    'clearance_certificate' => $validated['clearance_certificate'],
                    'visit_report' => $validated['visit_report'],
                    'remarks' => $validated['remarks'],
                ],
            ];
            LoggingService::logActivity($request, 'update', 'visits', $visit->id, $changes);

            // Clear existing site mappings
            $visitSiteMapping = VisitSiteMapping::where('visit_id', $visit->id);
            $visitSiteMapping->delete();
            $recordId = $visitSiteMapping->id;
            $changes = ['action' =>'Visit Site deleted'];
            LoggingService::logActivity($request, 'insert', 'visit_site_mappings', $recordId, $changes);

            // Insert new site mappings
            $siteCodes = $validated['site_code_id'];
            $sitesOfInspection = $validated['site_of_inspection'];
            $stateIds = $request->input('state_id');

            for ($i = 0; $i < count($siteCodes); $i++) {
                $visitSiteMapping = VisitSiteMapping::create([
                    'site_code_id' => $siteCodes[$i],
                    'site_of_inspection' => $sitesOfInspection[$i],
                    'visit_id' => $visit->id,
                    'state_id' => $stateIds[$i],
                ]);

                $recordId = $visitSiteMapping->id;
                $changes = ['action' =>'New Visit Site Mapping added'];
                LoggingService::logActivity($request, 'insert', 'visit_site_mappings', $recordId, $changes);
            }

            return response()->json(['success' => true, 'msg' => 'Visit updated successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation errors: ', $e->validator->errors()->all());
            return response()->json(['success' => false, 'msg' => 'Validation failed', 'errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error updating visit: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'Failed to update visit.']);
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


            $changes = [
                'action' => $isActive ? 'State restored' : 'State soft deleted'
            ];

            $action = $isActive ? 'restore' : 'delete';
            LoggingService::logActivity($request, $action, 'visits', $visit->id, $changes);

            if ($isActive) {
                if ($visit->trashed()) {
                    $visit->restore();
                }
            } else {
                if (!$visit->trashed()) {
                    $visit->delete();
                }
            }

            $visit->save();

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
}
