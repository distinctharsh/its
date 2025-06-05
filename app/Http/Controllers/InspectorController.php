<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Gender;
use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\Inspector;
use App\Models\Nationality;
use App\Models\PageLock;
use App\Models\PurposeOfDeletion;
use App\Models\Rank;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\LoggingService;

class InspectorController extends Controller
{

    public function manageInspector()
    {
        $inspectors = Inspector::withTrashed()
            ->with(['rank', 'designation', 'nationality' => function ($query) {
                $query->withTrashed(); // Include trashed nationalities
            }, 'inspections.category:id,category_name']) // Load inspections with related category and status
            ->latest()
            ->get();
        $genders = Gender::all();
        $nationalities  = Nationality::all();
        $ranks = Rank::all();

        // 🔒 Check if there's an active lock for this page
        $inspectorLock = PageLock::where('page', 'inspector') // make sure the page name matches DB
            ->where('locked', true)
            ->first();
        return view('manage-inspector', compact('inspectors', 'genders', 'nationalities', 'ranks', 'inspectorLock'));
    }

    public function show($id)
    {
        $inspector = DB::table('inspectors')
            ->join('nationalities', 'inspectors.nationality_id', '=', 'nationalities.id')
            ->leftJoin('ranks', 'inspectors.rank_id', '=', 'ranks.id')
            ->select(
                'inspectors.*',
                'nationalities.country_name as country',
                'ranks.rank_name as rank_name'
            )
            ->where('inspectors.id', $id)
            ->first();

            // 🔒 Check if there's an active lock for this page
        $inspectorLock = PageLock::where('page', 'inspector') // make sure the page name matches DB
            ->where('locked', true)
            ->first();

        if (!$inspector) {
            abort(404);
        }

        // Retrieve inspections for the inspector, along with category and status names
        $inspections = Inspection::where('inspector_id', $id)
            ->with(['category:id,category_name'])
            ->get();

        return view('inspector-details', compact('inspector', 'inspections', 'inspectorLock'));
    }




    public function addInspector()
    {
        $genders = Gender::whereNull('deleted_at')->get();
        $nationalities = Nationality::whereNull('deleted_at')->get();
        $ranks = Rank::whereNull('deleted_at')->get();
        $statuses = Status::whereNull('deleted_at')->get();
        $designations = Designation::whereNull('deleted_at')->get();
        $purposeOfDeletions = PurposeOfDeletion::whereNull('deleted_at')->get();
        $departments = Department::whereNull('deleted_at')->get();

        $inspection_categories = InspectionCategory::with('types')->get();
        return view('add-inspector', compact(
            'genders',
            'nationalities',
            'ranks',
            'statuses',
            'inspection_categories',
            'designations',
            'purposeOfDeletions',
            'departments'
        ));
    }


    public function createInspector(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');

            // if ($captchaInput !== $captchaSession) {
            //     return response()->json([
            //         'success' => false,
            //         'msg' => 'Captcha is not valid'
            //     ], 422);
            // }

            $nowIST = Carbon::now('Asia/Kolkata');
            // dd($nowIST);
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'gender' => 'required|exists:genders,id',
                'dob' => 'required|date|before:today',
                'nationality' => 'required|exists:nationalities,id',
                'place_of_birth' => 'required|max:255',
                'passport_number' => [
                    'required',
                    'max:255',
                    'unique:inspectors,passport_number',
                    'regex:/^[A-Z0-9]+$/',
                ],
                'unlp_number' => 'nullable|max:255|regex:/^[A-Z0-9]+$/',
                'rank' => 'required|exists:ranks,id',
                'designationId' => 'required|exists:designations,id',
                'qualifications' => 'required',
                'professional_experience' => 'required',
                'ib_clearance' => 'nullable|file|mimes:pdf|max:5120',
                'raw_clearance' => 'nullable|file|mimes:pdf|max:5120',
                'mea_clearance' => 'nullable|file|mimes:pdf|max:5120',
                
                // Routine fields
                'routine_category_id' => 'nullable|exists:inspection_categories,id',
                'routine_objection_department' => 'nullable|exists:departments,id',
                'date_of_joining' => 'nullable|date',
                'routine_deletion_date' => 'nullable|date',
                'routine_purpose_of_deletion' => 'nullable',
                // 'routine_status_id' => 'nullable|exists:statuses,id',
                'routine_remarks' => 'nullable|max:500',
                'routine_objection_document' => 'nullable|file|mimes:pdf|max:5120',
                
                
                // Challenge fields
                'challenge_category_id' => 'nullable|exists:inspection_categories,id',
                'challenge_objection_department' => 'nullable|exists:departments,id',
                'challenge_purpose_of_deletion' => 'nullable',
                'challenge_date_of_joining' => 'nullable|date',
                'challenge_deletion_date' => 'nullable|date',
                'challenge_remarks' => 'nullable|max:500',
                // 'challenge_status_id' => 'nullable|exists:statuses,id',
                'challenge_objection_document' => 'nullable|file|mimes:pdf|max:5120',


                'ib_status_id' => 'nullable|exists:statuses,id',
                'raw_status_id' => 'nullable|exists:statuses,id',
                'mea_status_id' => 'nullable|exists:statuses,id',
            ]);

     
            // Custom validation to ensure at least one section is filled
            if (empty($validatedData['routine_category_id']) && empty($validatedData['challenge_category_id'])) {
                return response()->json([
                    'success' => false,
                    'msg' => 'At least one inspection category (Routine or Challenge) must be filled.',
                ], 422);
            }

            // Custom validation to ensure 'date_of_joining' and 'routine_deletion_date' are required if 'routine_category_id' is present
            // if (!empty($validatedData['routine_category_id'])) {
            //     $request->validate([
            //         'routine_deletion_date' => 'required|date',
            //     ]);
            // }
            // if (!empty($validatedData['challenge_category_id'])) {
            //     $request->validate([
            //         'challenge_deletion_date' => 'required|date',
            //     ]);
            // }

            // Convert the date fields to the correct format (YYYY-MM-DD) using Carbon
           // Update this code to handle null values:
            if (!empty($validatedData['routine_deletion_date'])) {
                $validatedData['routine_deletion_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['routine_deletion_date'])->format('Y-m-d');
            } else {
                $validatedData['routine_deletion_date'] = null;
            }

            if (!empty($validatedData['challenge_deletion_date'])) {
                $validatedData['challenge_deletion_date'] = Carbon::createFromFormat('d-m-Y', $validatedData['challenge_deletion_date'])->format('Y-m-d');
            } else {
                $validatedData['challenge_deletion_date'] = null;
            }

            // Handle file upload
            $ibClearancePath = null;
            if ($request->hasFile('ib_clearance')) {
                $ibClearancePath = $request->file('ib_clearance')->store('ib_clearances');
            }
            $rawClearancePath = null;
            if ($request->hasFile('raw_clearance')) {
                $rawClearancePath = $request->file('raw_clearance')->store('raw_clearances');
            }
            $meaClearancePath = null;
            if ($request->hasFile('mea_clearance')) {
                $meaClearancePath = $request->file('mea_clearance')->store('mea_clearances');
            }


            $routineObjectionDocumentPath = null;
            if ($request->hasFile('routine_objection_document')) {
                $routineObjectionDocumentPath = $request->file('routine_objection_document')->store('routine_objection_files');
            }
    
            $challengeObjectionDocumentPath = null;
            if ($request->hasFile('challenge_objection_document')) {
                $challengeObjectionDocumentPath = $request->file('challenge_objection_document')->store('challenge_objection_files');
            }


            // Create the inspector
            $inspector = Inspector::create([
                'name' => $validatedData['name'],
                'gender_id' => $validatedData['gender'],
                'dob' => $validatedData['dob'],
                'nationality_id' => $validatedData['nationality'],
                'place_of_birth' => $validatedData['place_of_birth'],
                'passport_number' => $validatedData['passport_number'],
                'unlp_number' => $validatedData['unlp_number'],
                'rank_id' => $validatedData['rank'],
                'designation_id' => $validatedData['designationId'],
                'qualifications' => $validatedData['qualifications'],
                'professional_experience' => $validatedData['professional_experience'],
                'ib_clearance' => $ibClearancePath,
                'raw_clearance' => $rawClearancePath,
                'mea_clearance' => $meaClearancePath,

                'ib_status_id' => $validatedData['ib_status_id'],
                'raw_status_id' => $validatedData['raw_status_id'],
                'mea_status_id' => $validatedData['mea_status_id'],

                'created_at' => $nowIST,

                'is_draft' => strtolower(auth()->user()->role->name) === 'user',
                'is_reverted' => 0,


            ]);

            // dd($request);

          



            // Create a new inspection record for routine if it's provided
            if (!empty($validatedData['routine_category_id'])) {
                $inspectionRoutine = Inspection::create([
                    'inspector_id' => $inspector->id,
                    'category_id' => $validatedData['routine_category_id'],
                    'objection_department_id' => $validatedData['routine_objection_department'],
                    // 'category_type_id' => $validatedData['routine_category_type_id'],
                    'date_of_joining' => $validatedData['date_of_joining'],
                    'deletion_date' => $validatedData['routine_deletion_date'],
                    'purpose_of_deletion' => $validatedData['routine_purpose_of_deletion'],
                    'routine_objection_document' => $routineObjectionDocumentPath,
                    'status_id' => 1,
                    'remarks' => $validatedData['routine_remarks'],
                    'created_by' => auth()->id(),
                    'created_at' => $nowIST,
                ]);
                
                // $recordId = $inspectionRoutine->id;
                // $changes = ['action' => 'New Inspection Profile added'];
                // LoggingService::logActivity($request, 'insert', 'Inspection Profile', $recordId, $changes);
            }
            
            // Create a new inspection record for challenge if it's provided
            if (!empty($validatedData['challenge_category_id'])) {
                $inspectionChallenge = Inspection::create([
                    'inspector_id' => $inspector->id,
                    'category_id' => $validatedData['challenge_category_id'],
                    'objection_department_id' => $validatedData['challenge_objection_department'],
                    // 'category_type_id' => $validatedData['challenge_category_type_id'],
                    'date_of_joining' => $validatedData['challenge_date_of_joining'],
                    'deletion_date' => $validatedData['challenge_deletion_date'],
                    'purpose_of_deletion' => $validatedData['challenge_purpose_of_deletion'],
                    'challenge_objection_document' => $challengeObjectionDocumentPath, 
                    'remarks' => $validatedData['challenge_remarks'],
                    // 'status_id' => $validatedData['challenge_status_id'],
                    'created_by' => auth()->id(),
                    'created_at' => $nowIST,
                ]);

                // $recordId = $inspectionChallenge->id;
                // $changes = ['action' => 'New Inspection Profile added'];
                // LoggingService::logActivity($request, 'insert', 'Inspection Profile', $recordId, $changes);
            }


            // Commit the transaction if everything goes well
            DB::commit();

            // Logging
            // $recordId = $inspector->id;
            // $changes = ['action' => 'New Inspector added'];
            // LoggingService::logActivity($request, 'insert', 'inspectors', $recordId, $changes);

            // With this more detailed logging:
            $recordId = $inspector->id;
            $changes = [
                'action' => 'New Inspector created by ' . auth()->user()->name,
                'details' => [
                    'Inspector Name' => $inspector->name,
                    'Gender' => $inspector->gender_id == 1 ? 'Male' : 'Female',
                    'Date of Birth' => $inspector->dob,
                    'Nationality' => $inspector->nationality->country_name ?? null,
                    'Place of Birth' => $inspector->place_of_birth,
                    'Passport Number' => $inspector->passport_number,
                    'UNLP Number' => $inspector->unlp_number,
                    'Rank' => $inspector->rank->rank_name ?? null, 
                    'Designation' => $inspector->designation->designation_name ?? null,
                    'Qualifications' => $inspector->qualifications,
                    'Professional_experience' => $inspector->professional_experience,
                    'IB Clearance' => $inspector->ib_clearance ? 'File uploaded' : null,
                    'RAW Clearance' => $inspector->raw_clearance ? 'File uploaded' : null,
                    'MEA Clearance' => $inspector->mea_clearance ? 'File uploaded' : null,
                    'IB Status' => $inspector->ibStatus->status_name ?? null,
                    'RAW Status' => $inspector->rawStatus->status_name ?? null,
                    'MEA Status' => $inspector->meaStatus->status_name ?? null,
                    'Routine Inspection' => !empty($validatedData['routine_category_id']) ? 'Added' : null,
                    'Challenge Inspection' => !empty($validatedData['challenge_category_id']) ? 'Added' : null
                ]
            ];
            LoggingService::logActivity($request, 'insert', 'inspectors', $recordId, $changes);



            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspector Created!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {

            DB::rollBack();
            if (isset($inspector)) {
                $inspector->forceDelete();
            }

            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function editInspector($id)
    {
        try {
            $inspector = Inspector::withTrashed()->findOrFail($id);
            $genders = Gender::all();
            $nationalities = Nationality::withTrashed()->get();
            $ranks = Rank::all();
            $statuses = Status::all();
            $inspection_categories = InspectionCategory::with('types')->get();
            $designations = Designation::withTrashed()->get();

            $purposeOfDeletions = PurposeOfDeletion::whereNull('deleted_at')->get();
            $departments = Department::whereNull('deleted_at')->get();

            // Retrieve routine and challenge inspections
            $routineInspection = Inspection::where('inspector_id', $id)
                ->whereHas('category', function ($query) {
                    $query->where('is_challenge', 0);
                })
                ->first();

            $challengeInspection = Inspection::where('inspector_id', $id)
                ->whereHas('category', function ($query) {
                    $query->where('is_challenge', 1);
                })
                ->first();

            // dd($routineInspection, $challengeInspection);


            return view('edit-inspector', compact('inspector', 'genders', 'nationalities', 'ranks',
             'inspection_categories', 'statuses', 'designations', 'routineInspection', 'challengeInspection', 'purposeOfDeletions', 'departments'));
        } catch (\ModelNotFoundException $e) {
            return redirect()->route('manageInspector')->withErrors('Inspector not found.');
        } catch (\Exception $e) {
            return redirect()->route('manageInspector')->withErrors('An error occurred. Please try again.');
        }
    }





    public function updateInspector(Request $request, $id)
    {
        try {

            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');
            // if ($captchaInput !== $captchaSession) {
            //     return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            // }
            
            // Validate the request
            $validated = $request->validate([
                'name' => 'required|max:255',
                'gender' => 'required|exists:genders,id',
                'dob' => 'required|date|before:-18 years',
                'nationality' => 'required|exists:nationalities,id',
                'place_of_birth' => 'required|max:255',
                'passport_number' => [
                    'required',
                    'max:255',
                    Rule::unique('inspectors')->ignore($id),
                ],
                'unlp_number' => 'nullable|max:255',
                'rank' => 'required|exists:ranks,id',
                'designation_id' => 'required|exists:designations,id',
                'qualifications' => 'required',
                'professional_experience' => 'required',
                'ib_clearance' => 'nullable|file|mimes:pdf|max:5120',
                'raw_clearance' => 'nullable|file|mimes:pdf|max:5120',
                'mea_clearance' => 'nullable|file|mimes:pdf|max:5120',
                

                // Routine fields
                'routine_category_id' => 'nullable|exists:inspection_categories,id',
                'routine_objection_department' => 'nullable|exists:departments,id',
                // 'routine_category_type_id' => 'nullable|exists:inspection_category_types,id',
                'routine_joining_date' => 'nullable|date|required_with:routine_category_id',
                'routine_deletion_date' => 'nullable|date',
                'routine_purpose_of_deletion' => 'nullable|required_with:routine_deletion_date',
                // 'routine_status_id' => 'nullable|exists:statuses,id',
                'routine_remarks' => 'nullable|max:500',
                'routine_objection_document' => 'nullable|file|mimes:pdf|max:5120',

                // Challenge fields
                'challenge_category_id' => 'nullable|exists:inspection_categories,id',
                'challenge_objection_department' => 'nullable|exists:departments,id',
                'challenge_purpose_of_deletion' => 'nullable',
                // 'challenge_category_type_id' => 'nullable|exists:inspection_category_types,id',
                'challenge_date_of_joining' => 'nullable|date|required_with:challenge_category_id',
                'challenge_deletion_date' => 'nullable|date',
                'challenge_remarks' => 'nullable|max:500',
                // 'challenge_status_id' => 'nullable|exists:statuses,id',
                'challenge_objection_document' => 'nullable|file|mimes:pdf|max:5120',


                'ib_status_id' => 'nullable|exists:statuses,id',
                'raw_status_id' => 'nullable|exists:statuses,id',
                'mea_status_id' => 'nullable|exists:statuses,id',

              
            ]);

            $validated['nationality_id'] = $validated['nationality'];
            unset($validated['nationality']); // Remove the original key to avoid confusion


       

            $inspector = Inspector::withTrashed()->findOrFail($id);

            $originalData = $inspector->only(array_keys($validated));

            // Handle file upload
            if ($request->hasFile('ib_clearance')) {
                $file = $request->file('ib_clearance');
                $path = $file->store('ib_clearances');
                $validated['ib_clearance'] = $path;
            } else {
                $validated['ib_clearance'] = $inspector->ib_clearance;
            }
            // Handle file upload       
            if ($request->hasFile('raw_clearance')) {
                $file = $request->file('raw_clearance');
                $path = $file->store('raw_clearances');
                $validated['raw_clearance'] = $path;
            } else {
                $validated['raw_clearance'] = $inspector->raw_clearance;
            }
            // Handle file upload
            if ($request->hasFile('mea_clearance')) {
                $file = $request->file('mea_clearance');
                $path = $file->store('mea_clearances');
                $validated['mea_clearance'] = $path;
            } else {
                $validated['mea_clearance'] = $inspector->mea_clearance;
            }

           
            if ($request->hasFile('routine_objection_document')) {
                $file = $request->file('routine_objection_document');
                $path = $file->store('objection_files/routine'); // Store in the 'routine' folder
                $validated['routine_objection_document'] = $path; // Update the key to 'routine_objection_document'
            } else {
                // If no new file uploaded, retain the existing file from the inspector or inspection
                $validated['routine_objection_document'] = $inspector->routine_objection_document ?? null;
            }
            
            if ($request->hasFile('challenge_objection_document')) {
                $file = $request->file('challenge_objection_document');
                $path = $file->store('objection_files/challenge'); // Store in the 'challenge' folder
                $validated['challenge_objection_document'] = $path; // Update the key to 'challenge_objection_document'
            } else {
                // If no new file uploaded, retain the existing file from the inspector or inspection
                $validated['challenge_objection_document'] = $inspector->challenge_objection_document ?? null;
            }



            $validated['rank_id'] = $validated['rank'];


            // Add conditional update fields
                $validated['is_draft'] = strtolower(auth()->user()->role->name) === 'user';
                $validated['is_reverted'] = 0;

            $inspector->update($validated);

            // Log inspector changes
            $changes = [
                'action' => 'Inspector updated by ' . auth()->user()->name,
                'old_data' => $originalData,
                'new_data' => $validated
            ];
            LoggingService::logActivity($request, 'update', 'inspectors', $inspector->id, $changes);

            // dd($request);
            // Update or create the routine inspection record if inspector_id matches
            // Handle Routine Inspection: Delete if removed
            if (empty($validated['routine_category_id'])) {
                $existingRoutineInspection = Inspection::where('inspector_id', $id)
                    ->whereHas('category', function ($query) {
                        $query->where('is_challenge', 0);
                    })
                    ->first();

                if ($existingRoutineInspection) {
                    $existingRoutineInspection->delete();  // Delete the inspection if routine category is removed
                    LoggingService::logActivity($request, 'delete', 'inspections', $existingRoutineInspection->id, ['action' => 'Routine Inspection deleted']);
                }
            } else {
                // Update or Create the routine inspection if category is provided
                $routineInspectionData = [
                    'category_id' => $validated['routine_category_id'] ?? null,
                    'objection_department_id' => $validated['routine_objection_department'] ?? null,
                    'date_of_joining' => $validated['routine_joining_date'] ?? null,
                    'deletion_date' => $validated['routine_deletion_date'] ?? null,
                    'purpose_of_deletion' => $validated['routine_purpose_of_deletion'] ?? null,
                    'routine_objection_document' => $validated['routine_objection_document'], // Updated to 'routine_objection_document'
               
                    // 'status_id' => $validated['routine_status_id'] ?? null,
                    'remarks' => $validated['routine_remarks'] ?? null,
                    'created_by' => auth()->id(),
                ];

                $existingRoutineInspection = Inspection::where('inspector_id', $id)
                    ->whereHas('category', function ($query) {
                        $query->where('is_challenge', 0);
                    })
                    ->first();

                if ($existingRoutineInspection) {
                    $originalRoutineData = $existingRoutineInspection->only(array_keys($routineInspectionData));
                    $existingRoutineInspection->update($routineInspectionData);
                    $routineChanges = ['old_data' => $originalRoutineData, 'new_data' => $routineInspectionData];
                    LoggingService::logActivity($request, 'update', 'inspections', $existingRoutineInspection->id, $routineChanges);
                } else {
                    $inspectionRecord = Inspection::create(array_merge($routineInspectionData, ['inspector_id' => $inspector->id]));
                    $recordId = $inspectionRecord->id;
                    $changes = ['action' => 'New Routine Inspection added'];
                    LoggingService::logActivity($request, 'insert', 'inspections', $recordId, $changes);
                }
            }

            // Handle Challenge Inspection: Delete if removed
            if (empty($validated['challenge_category_id'])) {
                $existingChallengeInspection = Inspection::where('inspector_id', $id)
                    ->whereHas('category', function ($query) {
                        $query->where('is_challenge', 1);
                    })
                    ->first();

                if ($existingChallengeInspection) {
                    $existingChallengeInspection->delete();  // Delete the inspection if challenge category is removed
                    LoggingService::logActivity($request, 'delete', 'inspections', $existingChallengeInspection->id, ['action' => 'Challenge Inspection deleted']);
                }
            } else {
                // Update or Create the challenge inspection if category is provided
                $challengeInspectionData = [
                    'category_id' => $validated['challenge_category_id'] ?? null,
                    'objection_department_id' => $validated['challenge_objection_department'] ?? null,
                    'date_of_joining' => $validated['challenge_date_of_joining'] ?? null,
                    'deletion_date' => $validated['challenge_deletion_date'] ?? null,
                    'purpose_of_deletion' => $validated['challenge_purpose_of_deletion'] ?? null,
                    'challenge_objection_document' => $validated['challenge_objection_document'],
                    'remarks' => $validated['challenge_remarks'] ?? null,
                    // 'status_id' => $validated['challenge_status_id'] ?? null,
                    'created_by' => auth()->id(),
                ];

                $existingChallengeInspection = Inspection::where('inspector_id', $id)
                    ->whereHas('category', function ($query) {
                        $query->where('is_challenge', 1);
                    })
                    ->first();

                if ($existingChallengeInspection) {
                    $originalChallengeData = $existingChallengeInspection->only(array_keys($challengeInspectionData));
                    $existingChallengeInspection->update($challengeInspectionData);
                    $challengeChanges = ['old_data' => $originalChallengeData, 'new_data' => $challengeInspectionData];
                    LoggingService::logActivity($request, 'update', 'inspections', $existingChallengeInspection->id, $challengeChanges);
                } else {
                    $inspectionRecord = Inspection::create(array_merge($challengeInspectionData, ['inspector_id' => $inspector->id]));
                    $recordId = $inspectionRecord->id;
                    $changes = ['action' => 'New Challenge Inspection added'];
                    LoggingService::logActivity($request, 'insert', 'inspections', $recordId, $changes);
                }
            }



            // Clear the CAPTCHA session
            // $request->session()->forget('captcha_text');

            $recordId = $inspector->id;
            $changes = ['action' => 'Inspector updated'];

            LoggingService::logActivity($request, 'insert', 'inspections', $recordId, $changes);

            return response()->json(['success' => true, 'msg' => 'Inspector updated successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'msg' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }




    public function deleteInspector(Request $request)
    {
        try {
            $inspector = Inspector::withTrashed()->findOrFail($request->inspector_id);
            $inspector->save();

            $inspector->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Inspector deleted and status updated!'
            ]);
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


    public function updateInspectorStatus(Request $request, $id)
{
    try {
        $inspector = Inspector::withTrashed()->findOrFail($id);
        $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

        if ($isActive) {
            if ($inspector->trashed()) {
                $originalStatus = 'Deleted';
                $newStatus = 'Active';
                $inspector->restore();
                $inspector->save();

                // Log activity for restoring the inspector
                $changes = [
                    'action' => 'Inspector Restored by ' . auth()->user()->name,
                    'details' => [
                        'inspector_id' => $inspector->id,
                        'inspector_name' => $inspector->name,
                        'passport_number' => $inspector->passport_number,
                        'previous_status' => $originalStatus,
                        'new_status' => $newStatus,
                        'restored_by' => auth()->user()->name,
                        'restored_at' => now()->format('Y-m-d H:i:s')
                    ]
                ];
                LoggingService::logActivity($request, 'restore', 'inspectors', $inspector->id, $changes);
            }
        } else {
            $originalStatus = 'Active';
            $newStatus = 'Deleted';
            
            // Log activity before deleting
            $changes = [
                'action' => 'Inspector Deleted by ' . auth()->user()->name,
                'details' => [
                    'inspector_id' => $inspector->id,
                    'inspector_name' => $inspector->name,
                    'passport_number' => $inspector->passport_number,
                    'previous_status' => $originalStatus,
                    'new_status' => $newStatus,
                    'deleted_by' => auth()->user()->name,
                    'deleted_at' => now()->format('Y-m-d H:i:s')
                ]
            ];
            LoggingService::logActivity($request, 'delete', 'inspectors', $inspector->id, $changes);

            // Soft delete the inspector
            $inspector->delete();
        }

        return response()->json([
            'success' => true,
            'msg' => 'Status Updated Successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'msg' => $e->getMessage()
        ], 500);
    }
}



    public function approve($id)
    {
        $inspector = Inspector::findOrFail($id);

        if (auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin' && $inspector->is_draft) {
               $originalData = $inspector->only(['is_draft', 'is_reverted', 'reverted_at']);
           
                $inspector->update([
                    'is_draft' => false,
                    'is_reverted' => false,
                    'reverted_at' => null
                ]);


             // Log the approval
            $changes = [
                'action' => 'Inspector approved by ' . auth()->user()->name,
                'details' => [
                    'inspector_id' => $inspector->id,
                    'inspector_name' => $inspector->name,
                    'previous_status' => 'Draft',
                    'new_status' => 'Approved',
                    'changes' => [
                        'is_draft' => ['from' => true, 'to' => false],
                        'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => false],
                        'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => null]
                    ]
                ]
            ];
            
            LoggingService::logActivity(request(), 'Approved', 'inspectors', $inspector->id, $changes);


            return redirect()->back()->with('success', 'Inspector approved successfully.');
        }

        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    
 
    public function revert($id)
    {
        $inspector = Inspector::findOrFail($id);
         $originalData = $inspector->only(['is_reverted', 'reverted_at', 'is_draft']);
            $inspector->update([
                'is_reverted' => true,
                'reverted_at' => now(),
                'is_draft' => 0
                // Note: We DON'T set is_draft to true here
            ]);


              // Log the revert action
                $changes = [
                    'action' => 'Inspector reverted by ' . auth()->user()->name,
                    'details' => [
                        'inspector_id' => $inspector->id,
                        'inspector_name' => $inspector->name,
                        'previous_status' => $originalData['is_draft'] ? 'Draft' : 'Approved',
                        'new_status' => 'Reverted',
                        'changes' => [
                            'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => true],
                            'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => now()],
                            'is_draft' => ['from' => $originalData['is_draft'], 'to' => false]
                        ]
                    ]
                ];
                
                LoggingService::logActivity(request(), 'Reverted', 'inspectors', $inspector->id, $changes);


            return redirect()->back()->with('success', 'Inspector marked as reverted successfully.');
    }

    public function sendToDraft($id)
    {
        $inspector = Inspector::findOrFail($id);
         $originalData = $inspector->only(['is_draft', 'is_reverted', 'reverted_at']);
    

        // if (auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin' && $inspector->is_reverted) {
            $inspector->update([
                'is_draft' => true,
                'is_reverted' => false,
                'reverted_at' => null,
            ]);



            // Log the move to draft action
            $changes = [
                'action' => 'Inspector moved to draft by ' . auth()->user()->name,
                'details' => [
                    'inspector_id' => $inspector->id,
                    'inspector_name' => $inspector->name,
                    'previous_status' => $originalData['is_reverted'] ? 'Reverted' : 'Approved',
                    'new_status' => 'Draft',
                    'changes' => [
                        'is_draft' => ['from' => $originalData['is_draft'], 'to' => true],
                        'is_reverted' => ['from' => $originalData['is_reverted'], 'to' => false],
                        'reverted_at' => ['from' => $originalData['reverted_at'], 'to' => null]
                    ]
                ]
            ];
            
            LoggingService::logActivity(request(), 'Pending for Approval', 'inspectors', $inspector->id, $changes);


            return redirect()->back()->with('success', 'Inspector moved to draft successfully.');
        // }

        // return redirect()->back()->with('error', 'Unauthorized or invalid action.');
    }



}
