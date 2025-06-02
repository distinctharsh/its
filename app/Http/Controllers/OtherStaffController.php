<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Gender;
use App\Models\InspectionCategory;
use App\Models\Inspector;
use App\Models\Nationality;
use App\Models\OpcwFax;
use App\Models\OtherStaff;
use App\Models\PageLock;
use App\Models\PurposeOfDeletion;
use App\Models\Rank;
use App\Models\Status;
use App\Services\LoggingService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OtherStaffController extends Controller
{
    public function manageOtherStaff()
    {
        $otherStaffs = OtherStaff::withTrashed()->with(['rank', 'designation', 'nationality'])->latest()->get();

        // 🔒 Check if there's an active lock for this page
        $otherStaffLock = PageLock::where('page', 'other_staff')
        ->where('locked', true)
        ->first();
       

        return view('manage-other-staff', compact('otherStaffs', 'otherStaffLock'));
    }

    public function addOtherStaff()
    {
        $genders = Gender::whereNull('deleted_at')->get();
        $nationalities = Nationality::whereNull('deleted_at')->get();
        $ranks = Rank::whereNull('deleted_at')->get();
        $statuses = Status::whereNull('deleted_at')->get();
        $designations = Designation::whereNull('deleted_at')->get();
        $purposeOfDeletions = PurposeOfDeletion::whereNull('deleted_at')->get();
        $departments = Department::whereNull('deleted_at')->get();
        $opcw_document_numbers = OpcwFax::whereNull('deleted_at')->pluck('fax_number', 'id');

        $inspection_categories = InspectionCategory::with('types')->get();
        return view('add-other-staff', compact(
            'genders',
            'nationalities',
            'ranks',
            'statuses',
            'inspection_categories',
            'designations',
            'purposeOfDeletions',
            'departments',
            'opcw_document_numbers'
        ));
    }



    public function createOtherStaff(Request $request)
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
                'place_of_birth' => 'required|max:255',
                'nationality' => 'required|exists:nationalities,id',
                'unlp_number' => 'nullable|max:255|regex:/^[A-Z0-9]+$/',
                'passport_number' => [
                    'required',
                    'max:255',
                    'unique:inspectors,passport_number',
                    'regex:/^[A-Z0-9]+$/',
                ],
                'designationId' => 'required|exists:designations,id',
                'rank' => 'required|exists:ranks,id',
                'qualifications' => 'required',
                'professional_experience' => 'required',
                'scope_of_access' => 'nullable',
                'security_status_id' => 'nullable|exists:statuses,id',
                'communication_date' => 'nullable|date',
                'deletion_date' => 'nullable|date',
                'remarks' => 'nullable|max:500',
            
            ]);

            // Create the inspector
            $inspector = OtherStaff::create([
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
                'scope_of_access' => $validatedData['scope_of_access'],
                'security_status' => $validatedData['security_status_id'],
                'opcw_communication_date' => $validatedData['communication_date'],
                'deletion_date' => $validatedData['deletion_date'],
            
                'remarks' => $validatedData['remarks'],
                'created_at' => $nowIST,

                'is_draft' => strtolower(auth()->user()->role->name) === 'user',
                'is_reverted' => 0,
            ]);

            DB::commit();
            $recordId = $inspector->id;
            $changes = ['action' => 'New Inspector added'];
            LoggingService::logActivity($request, 'insert', 'inspectors', $recordId, $changes);

            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Other Staff Created!'
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



    public function editOtherStaff($id)
    {
        try {
            $staff = OtherStaff::withTrashed()->findOrFail($id);
            $genders = Gender::all();
            $nationalities = Nationality::withTrashed()->get();
            $ranks = Rank::all();
            $statuses = Status::all();
            $inspection_categories = InspectionCategory::with('types')->get();
            $designations = Designation::withTrashed()->get();
            $purposeOfDeletions = PurposeOfDeletion::whereNull('deleted_at')->get();
            $departments = Department::whereNull('deleted_at')->get();
            return view('edit-other-staff', compact('staff', 'genders', 'nationalities', 'ranks',
             'inspection_categories', 'statuses', 'designations', 'purposeOfDeletions', 'departments'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('manageOtherStaff')->withErrors('Other Staff not found.');
        } catch (\Exception $e) {
            return redirect()->route('manageOtherStaff')->withErrors('An error occurred. Please try again.');
        }
    }


    public function updateOtherStaff(Request $request, $id)
    {
        try {
            // Validate captcha
            // $captchaInput = strtoupper($request->input('captcha')); // Case-insensitive
            // $captchaSession = strtoupper($request->session()->get('captcha_text'));

            // if ($captchaInput !== $captchaSession) {
            //     return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            // }

    
            // Validate input data
            $validated = $request->validate([
                'name' => 'required|max:255',
                'gender' => 'required|exists:genders,id',
                'dob' => 'required|date|before:today',
                'place_of_birth' => 'required|max:255',
                'nationality' => 'required|exists:nationalities,id',
                'unlp_number' => 'nullable|max:255|regex:/^[A-Z0-9]+$/',
                'passport_number' => [
                    'required',
                    'max:255',
                    'unique:other_staff,passport_number,' . $id,
                    'regex:/^[A-Z0-9]+$/',
                ],
                'designationId' => 'required|exists:designations,id',
                'rank' => 'required|exists:ranks,id',
                'qualifications' => 'required',
                'professional_experience' => 'required',
                'scope_of_access' => 'nullable',
                'security_status_id' => 'nullable|exists:statuses,id',
                'opcw_communication_date' => 'nullable|date',
                'deletion_date' => 'nullable|date',
                'remarks' => 'nullable|max:500',
            ]);
    
            
            $validated['nationality_id'] = $validated['nationality'];
            unset($validated['nationality']);
            $validated['gender_id'] = $validated['gender'];
            unset($validated['gender']);
            $validated['designation_id'] = $validated['designationId'];
            unset($validated['designationId']);

            $staff = OtherStaff::withTrashed()->findOrFail($id);

            $originalData = $staff->only(array_keys($validated));
    
            $validated['rank_id'] = $validated['rank']; 
            $staff->update($validated);

            $validated['is_draft'] = strtolower(auth()->user()->role->name) === 'user';
            $validated['is_reverted'] = 0;
    
            $changes = [
                'old_data' => $originalData,
                'new_data' => $validated,
            ];
            LoggingService::logActivity($request, 'update', 'other staff', $staff->id, $changes);
    
            // $request->session()->forget('captcha_text');

            return response()->json(['success' => true, 'msg' => 'OPCW Staff updated successfully.']);


        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors if validation fails
            return response()->json(['success' => false, 'msg' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Return generic error if something goes wrong
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }


    public function updateOtherStaffStatus(Request $request, $id)
    {
        try {
            $staff = OtherStaff::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($staff->trashed()) {
                    $staff->restore();
                    $staff->save();

                    // Log activity for restoring the inspector
                    $changes = ['action' => 'Other Staff restored'];
                    LoggingService::logActivity($request, 'restore', 'other_staff', $staff->id, $changes);
                }
            } else {
                // Log activity for soft deleting the inspector
                $changes = ['action' => 'Other Staff deleted'];
                LoggingService::logActivity($request, 'delete', 'other_staff', $staff->id, $changes);

                // Soft delete the inspector
                $staff->delete();
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



     public function approve($id)
    {
        $otherStaff = OtherStaff::findOrFail($id);

        if (auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin' && $otherStaff->is_draft) {
            $otherStaff->update([
                'is_draft' => false,
                'is_reverted' => false,
                'reverted_at' => null
            ]);

            return redirect()->back()->with('success', 'Inspector approved successfully.');
        }

        return redirect()->back()->with('error', 'Unauthorized action.');
    }

    
 
    public function revert($id)
    {
        $otherStaff = OtherStaff::findOrFail($id);
            $otherStaff->update([
                'is_reverted' => true,
                'reverted_at' => now(),
                'is_draft' => 0
                // Note: We DON'T set is_draft to true here
            ]);

            return redirect()->back()->with('success', 'Other Staff marked as reverted successfully.');
    }

    public function sendToDraft($id)
    {
        $otherStaff = OtherStaff::findOrFail($id);

        // if (auth()->user()->role && strtolower(auth()->user()->role->name) === 'admin' && $otherStaff->is_reverted) {
            $otherStaff->update([
                'is_draft' => true,
                'is_reverted' => false,
                'reverted_at' => null,
            ]);

            return redirect()->back()->with('success', 'otherStaff moved to draft successfully.');
        // }

        // return redirect()->back()->with('error', 'Unauthorized or invalid action.');
    }
    


}
