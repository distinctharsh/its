<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\InspectionCategoryType;
use Illuminate\Http\Request;

class InspectionCategoryTypeController extends Controller
{
    public function manageInspectionCategoryType(){
        $types = InspectionCategoryType::withTrashed()->get();
        return view('manage-inspection-category-type', compact('types'));
    }

    public function addInspectionCategoryType()
    {
        return view('add-inspection-category-type');
    }

    public function createInspectionCategoryType(Request $request)
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
                'type_name' => 'required|string|max:1000',
            ]);

            // Create a new status record
            $status = InspectionCategoryType::create([
                'type_name' => $validatedData['type_name'],
            ]);

            $recordId = $status->id;
            $changes = ['action' =>'New Inspection Category Type added'];
            LoggingService::logActivity($request, 'insert', 'inspection_categories', $recordId, $changes);

            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspectioon Category type Created!'
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

    public function editInspectionCategoryType($id)
    {
        try {
            $type = InspectionCategoryType::withTrashed()->findOrFail($id);
            return view('edit-inspection-category-type', compact('type'));
        } catch (\Exception $e) {
            \Log::error('Edit InspectionCategoryType Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the InspectionCategoryType.'], 500);
        }
    }

    public function updateInspectionCategoryType(Request $request, $id)
    {
        try {
            // CAPTCHA validation
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
                'type_name' => 'required|string|max:1000',
            ]);

            // Find the existing record
            $inspection_category_type = InspectionCategoryType::withTrashed()->findOrFail($id);

            // Find the existing record and capture old values
            $inspection_category_type = InspectionCategoryType::withTrashed()->findOrFail($id);
            $oldTypeName = $inspection_category_type->type_name;
            // Update the record
            $inspection_category_type->update([
                'type_name' => $validatedData['type_name'],
            ]);


            // Prepare log details for the update
            $changes = [
                'old_type_name' => $oldTypeName,
                'new_type_name' => $validatedData['type_name']
            ];
            LoggingService::logActivity($request, 'update', 'inspection_category_types', $inspection_category_type->id, $changes);

            // Clear the CAPTCHA session
            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspection Category Type Updated Successfully!'
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

    public function deleteInspectionCategoryType(Request $request)
    {
        try {
            $inspectionCategoryType = InspectionCategoryType::withTrashed()->findOrFail($request->status_id);
            $inspectionCategoryType->save();

            $recordId = $inspectionCategoryType->id;
            $changes = ['action' => 'Inspection Category Type deleted'];
            LoggingService::logActivity($request, 'delete', 'inspection_category_types', $recordId, $changes);
        
            $inspectionCategoryType->delete();

            return response()->json([
                'success' => true,
                'msg' => 'InspectionCategoryType Deleted!'
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

    public function updateInspectionCategoryTypeStatus(Request $request, $id)
    {
        try {
            $type = InspectionCategoryType::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($type->trashed()) {
                    $type->restore();
                    $type->save();
    
                    // Log the action for restore
                    $recordId = $type->id;
                    $changes = ['action' => 'Inspection Category Type restored'];
                    LoggingService::logActivity($request, 'restore', 'inspection_category_types', $recordId, $changes);
                }
            } else {
                // Log the action for delete
                $recordId = $type->id;
                $changes = ['action' => 'Inspection Category Type deleted'];
                LoggingService::logActivity($request, 'delete', 'inspection_category_types', $recordId, $changes);
    
                // Soft delete the inspection category type
                $type->delete();
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
