<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    //
    public function manageDesignation()
    {
        $user = Auth::user();
        $designations = Designation::withTrashed()->get();
        return view('manage-designation', compact('designations'));
    }

    public function addDesignation()
    {
        return view('add-designation');
    }

    public function createDesignation(Request $request)
    {
        try {
            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');

            // if ($captchaInput !== $captchaSession) {
            //     return response()->json([
            //         'success' => false,
            //         'msg' => 'Captcha is not valid'
            //     ], 422);
            // }

            // Validate the incoming request
            $validatedData = $request->validate([
                'designation_name' => 'required|string|max:1000',
            ]);

            // Create a new designation record
            $designation = Designation::create([
                'designation_name' => $validatedData['designation_name'],
            ]);

            $recordId = $designation->id;
            $changes = ['action' => 'New Designation added'];
            LoggingService::logActivity($request, 'insert', 'designations', $recordId, $changes);

            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Designation Created Successfully!'
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

    public function editDesignation($id)
    {
        try {
            $designation = Designation::withTrashed()->findOrFail($id);
            return view('edit-designation', compact('designation'));
        } catch (\Exception $e) {
            \Log::error('Edit Designation Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the designation.'], 500);
        }
    }

    public function updateDesignation(Request $request, $id)
    {
        try {
            // CAPTCHA validation
            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');

            // if ($captchaInput !== $captchaSession) {
            //     return response()->json([
            //         'success' => false,
            //         'msg' => 'Captcha is not valid'
            //     ], 422);
            // }

            // Validate the incoming request
            $validatedData = $request->validate([
                'designation_name' => 'required|string|max:1000',
            ]);

            // Find the existing record
            $designation = Designation::withTrashed()->findOrFail($id);

            // Capture the original designation name for logging
            $originalDesignationName = $designation->designation_name;

            // Update the record
            $designation->update([
                'designation_name' => $validatedData['designation_name'],
            ]);

            // Log the changes
            $changes = [
                'old_data' => [
                    'designation_name' => $originalDesignationName,
                ],
                'new_data' => [
                    'designation_name' => $validatedData['designation_name'],
                ]
            ];
            LoggingService::logActivity($request, 'update', 'designations', $designation->id, $changes);

            // Clear the CAPTCHA session
            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Designation Updated Successfully!'
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

    public function deleteDesignation(Request $request)
    {
        try {
            $designation = Designation::withTrashed()->findOrFail($request->designation_id);

            $changes = ['action' => 'Designation status updated to inactive'];
            LoggingService::logActivity($request, 'update', 'designations', $designation->id, $changes);

          
            $designation->save();

            $designation->delete();

            $changes = ['action' => 'Designation deleted'];
            LoggingService::logActivity($request, 'delete', 'designations', $designation->id, $changes);

            return response()->json([
                'success' => true,
                'msg' => 'Designation deleted and status updated!'
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

    public function updateDesignationStatus(Request $request, $id)
    {
        try {
            $designation = Designation::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            // Handle restoring or soft deleting the designation
            if ($isActive) {
                if ($designation->trashed()) {
                    $designation->restore();
                    $designation->save();

                    // Log the status update activity for restore
                    $changes = ['action' => 'Designation restored'];
                    LoggingService::logActivity($request, 'restore', 'designation', $designation->id, $changes);
                }
            } else {
                // Log the status update activity for soft delete
                $changes = ['action' => 'Designation soft deleted'];
                LoggingService::logActivity($request, 'delete', 'designation', $designation->id, $changes);

                // Soft delete the designation
                $designation->delete();
            }

            $designation->save();

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
