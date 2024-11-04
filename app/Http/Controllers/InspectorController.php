<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\Inspector;
use App\Models\Nationality;
use App\Models\Rank;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class InspectorController extends Controller
{
    //

    public function manageInspector()
    {
        $inspectors = Inspector::withTrashed()
        ->with(['rank', 'nationality', 'inspections.category:id,category_name', 'inspections.status:id,status_name']) // Load inspections with related category and status
        ->get();
        $genders = Gender::all();
        $nationalities  = Nationality::all();
        $ranks = Rank::all();
        return view('manage-inspector', compact('inspectors', 'genders', 'nationalities', 'ranks'));
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

        if (!$inspector) {
            abort(404);
        }

        // Retrieve inspections for the inspector, along with category and status names
        $inspections = Inspection::where('inspector_id', $id)
            ->with(['category:id,category_name', 'status:id,status_name'])
            ->get();

        return view('inspector-details', compact('inspector', 'inspections'));
    }




    public function addInspector()
    {
        $genders = Gender::whereNull('deleted_at')->get();
        $nationalities = Nationality::whereNull('deleted_at')->get();
        $ranks = Rank::whereNull('deleted_at')->get();
        $statuses = Status::whereNull('deleted_at')->get();

        $inspection_categories = InspectionCategory::with('types')->get();
        return view('add-inspector', compact('genders', 'nationalities', 'ranks', 'statuses', 'inspection_categories'));
    }


    public function createInspector(Request $request)
    {
        try {
            $captchaInput = $request->input('captcha');
            $captchaSession = $request->session()->get('captcha_text');

            if ($captchaInput !== $captchaSession) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Captcha is not valid'
                ], 422);
            }

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
                'unlp_number' => 'nullable|max:255|unique:inspectors,unlp_number|regex:/^[A-Z0-9]+$/',
                'rank' => 'required|exists:ranks,id',
                'qualifications' => 'required',
                'professional_experience' => 'required',
                'clearance_certificate' => 'nullable|file|mimes:pdf|max:5120',
                'remarks' => 'nullable|max:500',
                // Routine fields
                'routine_category_id' => 'nullable|exists:inspection_categories,id',
                // 'routine_category_type_id' => 'nullable|exists:inspection_category_types,id',
                'date_of_joining' => 'nullable|date',
                'routine_status_id' => 'nullable|exists:statuses,id',
                // Challenge fields
                'challenge_category_id' => 'nullable|exists:inspection_categories,id',
                // 'challenge_category_type_id' => 'nullable|exists:inspection_category_types,id',
                'challenge_date_of_joining' => 'nullable|date',
                'challenge_status_id' => 'nullable|exists:statuses,id',
            ]);

            // Custom validation to ensure at least one section is filled
            if (empty($validatedData['routine_category_id']) && empty($validatedData['challenge_category_id'])) {
                return response()->json([
                    'success' => false,
                    'msg' => 'At least one inspection category (Routine or Challenge) must be filled.',
                ], 422);
            }


            // Handle file upload
            $clearanceCertificatePath = null;
            if ($request->hasFile('clearance_certificate')) {
                $clearanceCertificatePath = $request->file('clearance_certificate')->store('clearance_certificates');
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
                'qualifications' => $validatedData['qualifications'],
                'professional_experience' => $validatedData['professional_experience'],
                'clearance_certificate' => $clearanceCertificatePath,
                'remarks' => $validatedData['remarks'],
                'created_at' => $nowIST,

            ]);

            // Create a new inspection record for routine if it's provided
            if (!empty($validatedData['routine_category_id'])) {
                $inspectionRoutine = Inspection::create([
                    'inspector_id' => $inspector->id,
                    'category_id' => $validatedData['routine_category_id'],
                    // 'category_type_id' => $validatedData['routine_category_type_id'],
                    'date_of_joining' => $validatedData['date_of_joining'],
                    'status_id' => $validatedData['routine_status_id'],
                    'created_by' => auth()->id(),
                    'created_at' => $nowIST,
                ]);
            }

            // Create a new inspection record for challenge if it's provided
            if (!empty($validatedData['challenge_category_id'])) {
                $inspectionChallenge = Inspection::create([
                    'inspector_id' => $inspector->id,
                    'category_id' => $validatedData['challenge_category_id'],
                    // 'category_type_id' => $validatedData['challenge_category_type_id'],
                    'date_of_joining' => $validatedData['challenge_date_of_joining'],
                    'status_id' => $validatedData['challenge_status_id'],
                    'created_by' => auth()->id(),
                    'created_at' => $nowIST,
                ]);
            }



            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspector Created!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Return detailed error message for general exceptions
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
            $nationalities = Nationality::all();
            $ranks = Rank::all();
            $statuses = Status::all();
            $inspection_categories = InspectionCategory::with('types')->get();

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


            return view('edit-inspector', compact('inspector', 'genders', 'nationalities', 'ranks', 'inspection_categories', 'statuses', 'routineInspection', 'challengeInspection'));
        } catch (\ModelNotFoundException $e) {
            return redirect()->route('manageInspector')->withErrors('Inspector not found.');
        } catch (\Exception $e) {
            return redirect()->route('manageInspector')->withErrors('An error occurred. Please try again.');
        }
    }





    public function updateInspector(Request $request, $id)
    {
        try {
            // CAPTCHA validation
            $captchaInput = $request->input('captcha');
            $captchaSession = $request->session()->get('captcha_text');
            if ($captchaInput !== $captchaSession) {
                return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            }

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
                'qualifications' => 'required',
                'professional_experience' => 'required',
                'clearance_certificate' => 'nullable|file|mimes:pdf|max:2048',
                'remarks' => 'nullable|max:500',

                // Routine fields
                'routine_category_id' => 'nullable|exists:inspection_categories,id',
                // 'routine_category_type_id' => 'nullable|exists:inspection_category_types,id',
                'routine_joining_date' => 'nullable|date|required_with:routine_category_id', // Require if category is present
                'routine_status_id' => 'nullable|exists:statuses,id',

                // Challenge fields
                'challenge_category_id' => 'nullable|exists:inspection_categories,id',
                // 'challenge_category_type_id' => 'nullable|exists:inspection_category_types,id',
                'challenge_date_of_joining' => 'nullable|date|required_with:challenge_category_id', // Require if category is present
                'challenge_status_id' => 'nullable|exists:statuses,id',
            ]);

            $validated['nationality_id'] = $validated['nationality'];
            unset($validated['nationality']); // Remove the original key to avoid confusion

            $inspector = Inspector::withTrashed()->findOrFail($id);

            // Handle file upload
            if ($request->hasFile('clearance_certificate')) {
                $file = $request->file('clearance_certificate');
                $path = $file->store('clearance_certificates');
                $validated['clearance_certificate'] = $path;
            } else {
                $validated['clearance_certificate'] = $inspector->clearance_certificate;
            }

            $validated['rank_id'] = $validated['rank'];

            $inspector->update($validated);

            // dd($request);
            // Update or create the routine inspection record if inspector_id matches
            if (!empty($validated['routine_category_id']) && !empty($validated['routine_joining_date'])) {
                $routineInspectionData = [
                    'category_id' => $validated['routine_category_id'],
                    'date_of_joining' => $validated['routine_joining_date'],
                    'status_id' => $validated['routine_status_id'] ?? null,
                    'created_by' => auth()->id(),
                ];


                $existingRoutineInspection = Inspection::where('inspector_id', $id)
                ->whereHas('category', function ($query) {
                    $query->where('is_challenge', 0);
                })
                ->first();

        

                // Check for existing record with the given inspector_id
                // $existingRoutineInspection = Inspection::where('inspector_id', $inspector->id)->where('category_id', $validated['routine_category_id'])->first();

                if ($existingRoutineInspection) {
                    // Update the existing record
                    $existingRoutineInspection->update($routineInspectionData);
                } else {
                    // Create a new record if none exists
                    Inspection::create(array_merge($routineInspectionData, ['inspector_id' => $inspector->id]));
                }
            }

            // Update or create the challenge inspection record if inspector_id matches
            if (!empty($validated['challenge_category_id']) && !empty($validated['challenge_date_of_joining'])) {
                $challengeInspectionData = [
                    'category_id' => $validated['challenge_category_id'],
                    'date_of_joining' => $validated['challenge_date_of_joining'],
                    'status_id' => $validated['challenge_status_id'] ?? null,
                    'created_by' => auth()->id(),
                ];

                $existingChallengeInspection = Inspection::where('inspector_id', $id)
                ->whereHas('category', function ($query) {
                    $query->where('is_challenge', 1);
                })
                ->first();


                // Check for existing record with the given inspector_id
                // $existingChallengeInspection = Inspection::where('inspector_id', $inspector->id)->where('category_id', $validated['challenge_category_id'])->first();

                if ($existingChallengeInspection) {
                    // Update the existing record
                    $existingChallengeInspection->update($challengeInspectionData);
                } else {
                    // Create a new record if none exists
                    Inspection::create(array_merge($challengeInspectionData, ['inspector_id' => $inspector->id]));
                }
            }


            // Clear the CAPTCHA session
            $request->session()->forget('captcha_text');

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
                    $inspector->restore();
                    $inspector->save();
                }
            } else {
                $inspector->delete();
            }

            return response()->json([
                'success' => true,
                'msg' => 'Status Updated Succesfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
