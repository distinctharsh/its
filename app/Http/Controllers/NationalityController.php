<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NationalityController extends Controller
{
    public function manageNationality()
    {
        $user = Auth::user();
        $nationalities = Nationality::whereNull('deleted_at')->get();

        if ($user->hasRole('Admin')) {
            $nationalities = Nationality::withTrashed()->get();
        } else {
            $nationalities = Nationality::whereNull('deleted_at')->get();
        }
        return view('manage-nationalities', compact('nationalities'));
    }

    public function addNationality()
    {
        return view('add-nationality');
    }

    public function createNationality(Request $request)
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

            
            // Validate the incoming request
            $validatedData = $request->validate([
                'country_name' => 'required|string|max:255',
            ]);

            // Create a new nationality record
            $nationality = Nationality::create([
                'country_name' => $validatedData['country_name'],
            ]);

            $recordId = $nationality->id;
            $changes = ['action' =>'New Nationality added'];
            LoggingService::logActivity($request, 'insert', 'nationalities', $recordId, $changes);

            return response()->json([
                'success' => true,
                'msg' => 'Nationality Created Successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'msg' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function editNationality($id)
    {
        try {
            $nationality = Nationality::withTrashed()->findOrFail($id);
            return view('edit-nationality', compact('nationality'));
        } catch (\Exception $e) {
            \Log::error('Edit Nationality Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the nationality.'], 500);
        }
    }

    public function updateNationality(Request $request, $id)
    {
        try {

            // CAPTCHA validation
            $captchaInput = $request->input('captcha');
            $captchaSession = $request->session()->get('captcha_text');

            if ($captchaInput !== $captchaSession) {
                return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            }

            
            // Validate the incoming request
            $validatedData = $request->validate([
                'country_name' => 'required|string|max:255',
            ]);

            // Find the existing record
            $nationality = Nationality::withTrashed()->findOrFail($id);

            // Capture the original data for logging
            $originalData = $nationality->only('country_name');

            // Update the record
            $nationality->update([
                'country_name' => $validatedData['country_name'],
            ]);

            // Log the change with both old and new data
            $changes = [
                'old_data' => $originalData,
                'new_data' => ['country_name' => $validatedData['country_name']]
            ];
            LoggingService::logActivity($request, 'update', 'nationalities', $nationality->id, $changes);


            return response()->json([
                'success' => true,
                'msg' => 'Nationality Updated Successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'msg' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteNationality(Request $request)
    {
        try {
            $nationality = Nationality::withTrashed()->findOrFail($request->nationality_id);
            $nationality->save();

            $recordId = $nationality->id;
            $changes = ['action' => 'Nationality deleted'];
            LoggingService::logActivity($request, 'delete', 'nationalities', $recordId, $changes);
        
            $nationality->delete(); 

            return response()->json([
                'success' => true,
                'msg' => 'Nationality deleted and status updated!'
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

    public function updateNationalityStatus(Request $request, $id)
    {
        try {
            $nationality = Nationality::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($nationality->trashed()) {
                    $nationality->restore();
                    $nationality->save();
                }
    
                // Log the status update activity for restore
                $changes = [
                    'action' => 'Nationality restored' // Action type is 'restore' when nationality is restored
                ];
                LoggingService::logActivity($request, 'restore', 'country', $nationality->id, $changes);
    
            } else {
                $nationality->delete();
    
                // Log the status update activity for soft delete
                $changes = [
                    'action' => 'Nationality soft deleted' // Action type is 'deleted' when nationality is soft deleted
                ];
                LoggingService::logActivity($request, 'delete', 'country', $nationality->id, $changes);
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
}
