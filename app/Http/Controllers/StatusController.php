<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function manageStatus(){
            $statuses = Status::withTrashed()->get();
        return view('manage-status', compact('statuses'));
    }


    public function addStatus()
    {   
        return view('add-status');
    }

    public function createStatus(Request $request)
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
                'status_name' => 'required|string|max:1000',
            ]);

            // Create a new status record
            $status = Status::create([
                'status_name' => $validatedData['status_name'],
            ]);

            $recordId = $status->id;
            $changes = ['action' =>'New Status added'];
            LoggingService::logActivity($request, 'insert', 'statuses', $recordId, $changes);

            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Status Created Successfully!'
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

    public function editStatus($id)
    {
        try {
            $status = Status::withTrashed()->findOrFail($id);
            return view('edit-status', compact('status'));
        } catch (\Exception $e) {
            \Log::error('Edit Status Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the status.'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
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
                'status_name' => 'required|string|max:1000',
            ]);

            // Find the existing record
            $status = Status::withTrashed()->findOrFail($id);

            // Capture the original status name for logging
            $originalData = [
                'status_name' => $status->status_name,
            ];

            // Update the record
            $status->update([
                'status_name' => $validatedData['status_name'],
            ]);

            // Log the changes
            $changes = [
                'old_data' => $originalData,
                'new_data' => [
                    'status_name' => $validatedData['status_name'],
                ],
            ];
            LoggingService::logActivity($request, 'update', 'statuses', $status->id, $changes);

            // Clear the CAPTCHA session
            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Status Updated Successfully!'
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

    public function deleteStatus(Request $request)
    {
        try {
            $status = Status::withTrashed()->findOrFail($request->status_id);
            $status->save();

            $changes = ['action' => 'Status deleted'];
            LoggingService::logActivity($request, 'delete', 'statuses', $status->id, $changes);

            $status->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Status Deleted!'
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

    public function updateStatusStatus(Request $request, $id)
    {
        try {
            $status = Status::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($status->trashed()) {
                    $status->restore();
                    $status->save();
    
                    // Log the status update activity for restore
                    $changes = [
                        'action' => 'Status restored'
                    ];
                    LoggingService::logActivity($request, 'restore', 'statuses', $status->id, $changes);
                }
            } else {
                // Soft delete the status
                $status->delete();
    
                // Log the status update activity for soft delete
                $changes = [
                    'action' => 'Status soft deleted'
                ];
                LoggingService::logActivity($request, 'delete', 'statuses', $status->id, $changes);
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

