<?php

namespace App\Http\Controllers;

use App\Models\InspectionIssue;
use App\Services\LoggingService;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    //

    public function manageIssue(Request $request){
        $issues = InspectionIssue::withTrashed()->get();
        return view("manage-issue", compact("issues"));
    }

    public function addIssue()
    {
        return view('add-issue');
    }

    public function createIssue(Request $request)
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
            // $request->session()->forget('captcha_text');
            $validatedData = $request->validate([
                'issue_name' => 'required|string|max:255',
            ]);

            $issue = InspectionIssue::create([
                'name' => $validatedData['issue_name'],    
            ]);
            $recordId = $issue->id;
            $changes = ['action' => 'New Issue added'];
            LoggingService::logActivity($request, 'insert', 'inspection_issues', $recordId, $changes);

            return response()->json([
                'success' => true,
                'msg' => 'User Created Successfully! Default password: 12345678'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->validator->errors()->first() 
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function editIssue($id)
    {
        $issue = InspectionIssue::findOrFail($id);
        return view('edit-issue', compact('issue'));
    }


    public function updateIssue(Request $request, $id)
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
                'issue_name' => 'required|string|max:1000',
            ]);

            // Find the existing record
            $issue = InspectionIssue::withTrashed()->findOrFail($id);

            // Capture the original status name for logging
            $originalData = [
                'name' => $issue->name,
            ];

            // Update the record
            $issue->update([
                'name' => $validatedData['issue_name'],
            ]);

            // Log the changes
            $changes = [
                'old_data' => $originalData,
                'new_data' => [
                    'name' => $validatedData['issue_name'],
                ],
            ];
            LoggingService::logActivity($request, 'update', 'inspection_issues', $issue->id, $changes);

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

    public function updateIssueStatus(Request $request, $id)
    {
        try {
            $issue = InspectionIssue::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($issue->trashed()) {
                    $issue->restore();
                    $issue->save();
                }
        
                // Log the status update activity for restore
                $changes = [
                    'action' => 'Issue restored' // Action type is 'restore' when the user is restored
                ];
                LoggingService::logActivity($request, 'restore', 'users', $issue->id, $changes);
        
            } else {
                $issue->delete();
        
                // Log the status update activity for soft delete
                $changes = [
                    'action' => 'Issue soft deleted' // Action type is 'deleted' when the user is soft deleted
                ];
                LoggingService::logActivity($request, 'delete', 'users', $issue->id, $changes);
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
