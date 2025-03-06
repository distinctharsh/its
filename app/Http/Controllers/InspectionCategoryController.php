<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\InspectionCategory;
use App\Models\InspectionCategoryType;
use App\Models\InspectionProperties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionCategoryController extends Controller
{
    //manageInspectionCategory

    public function manageInspectionCategory()
    {
        $user = Auth::user();

        $inspection_categories = InspectionCategory::withTrashed()->with('types')->get();

        return view('manage-inspection-category', compact('inspection_categories'));
    }

    public function manageCategoryInspection()
    {
        $user = Auth::user();

        $inspection_categories = InspectionProperties::withTrashed()->get();

        return view('manage-category-inspection', compact('inspection_categories'));
    }

    public function addInspectionCategory()
    {
        $inspection_types = InspectionCategoryType::all();
        return view('add-inspection-category', compact('inspection_types'));
    }
    
    public function addCategoryInspection()
    {
        $inspection_types = InspectionProperties::all();
        return view('add-category-inspection', compact('inspection_types'));
    }

    public function createInspectionCategory(Request $request)
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
                'category_name' => 'required|string|max:1000',
                'inspection_types' => 'required|array', // Validate inspection types
                'inspection_types.*' => 'exists:inspection_category_types,id'
            ]);

            // Create a new inspection categories record
            $inspection_category = InspectionCategory::create([
                'category_name' => $validatedData['category_name'],
            ]);

            $recordId = $inspection_category->id;
            $changes = ['action' =>'New inspection category added'];
            LoggingService::logActivity($request, 'insert', 'inspection_categories', $recordId, $changes);

            $inspection_category->types()->attach($validatedData['inspection_types']);

            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspection Categories Created Successfully!'
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
    public function createCategoryInspection(Request $request)
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
                'category_name' => 'required|string|max:1000',
                
            ]);

            // Create a new inspection categories record
            $inspection_category = InspectionProperties::create([
                'name' => $validatedData['category_name'],
            ]);

            $recordId = $inspection_category->id;
            $changes = ['action' =>'New inspection category added'];
            LoggingService::logActivity($request, 'insert', 'inspection_categories', $recordId, $changes);

        

            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspection Categories Created Successfully!'
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

    public function editInspectionCategory($id)
    {
        try {
            $inspection_category = InspectionCategory::withTrashed()->findOrFail($id);
            $inspection_types = InspectionCategoryType::all(); 
            return view('edit-inspection-category', compact('inspection_category', 'inspection_types'));
        } catch (\Exception $e) {
            \Log::error('Edit Inspection Categories Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the inspection category.'], 500);
        }
    }
    public function editCategoryInspection($id)
    {
        try {
            $inspection_category = InspectionProperties::withTrashed()->findOrFail($id);
            
            return view('edit-category-inspection', compact('inspection_category'));
        } catch (\Exception $e) {
            \Log::error('Edit Inspection Categories Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the inspection category.'], 500);
        }
    }

    public function updateInspectionCategory(Request $request, $id)
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

            $validatedData = $request->validate([
                'category_name' => 'required|string|max:1000',
                'inspection_types' => 'required|array', 
                'inspection_types.*' => 'exists:inspection_category_types,id' 
            ]);

            $inspection_category = InspectionCategory::withTrashed()->findOrFail($id);

            // Capture old values for logging
            $oldCategoryName = $inspection_category->category_name;
            $oldInspectionTypes = $inspection_category->types()->pluck('id')->toArray();


            $inspection_category->update([
                'category_name' => $validatedData['category_name'],
            ]);

            $inspection_category->types()->sync($validatedData['inspection_types']);

            $changes = [
                'old_category_name' => $oldCategoryName,
                'new_category_name' => $validatedData['category_name'],
                'old_inspection_types' => $oldInspectionTypes,
                'new_inspection_types' => $validatedData['inspection_types']
            ];
            LoggingService::logActivity($request, 'update', 'inspection_categories', $inspection_category->id, $changes);


            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspection Category Updated Successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

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
    public function updateCategoryInspection(Request $request, $id)
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

            $validatedData = $request->validate([
                'category_name' => 'required|string|max:1000',
            ]);
          

            $inspection_category = InspectionProperties::withTrashed()->findOrFail($id);

            // Capture old values for logging
            $oldCategoryName = $inspection_category->name;
         


            $inspection_category->update([
                'name' => $validatedData['category_name'],
            ]);

           

            $changes = [
                'old_category_name' => $oldCategoryName,
                'new_category_name' => $validatedData['category_name'],
            ];
            LoggingService::logActivity($request, 'update', 'inspection_categories', $inspection_category->id, $changes);


            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspection Category Updated Successfully!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

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

    public function deleteInspectionCategory(Request $request)
    {
        try {
            $inspection_category = InspectionCategory::withTrashed()->findOrFail($request->inspection_category_id);

            $recordId = $inspection_category->id;
            $changes = ['action' => 'Inspection Category deleted'];
            LoggingService::logActivity($request, 'delete', 'inspection_categories', $recordId, $changes);

            // Perform soft delete
            $inspection_category->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Inspection Category deleted successfully!'
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


    public function updateInspectionCategoryStatus(Request $request, $id)
    {
        try {
            $inspection_category = InspectionCategory::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($inspection_category->trashed()) {
                    $inspection_category->restore();
    
                    // Log the status update activity for restore
                    $recordId = $inspection_category->id;
                    $changes = ['action' => 'Inspection Category restored'];
                    LoggingService::logActivity($request, 'restore', 'inspection_categories', $recordId, $changes);
                }
            } else {
                // Log the status update activity for soft delete
                $recordId = $inspection_category->id;
                $changes = ['action' => 'Inspection Category deleted'];
                LoggingService::logActivity($request, 'delete', 'inspection_categories', $recordId, $changes);
    
                // Soft delete the inspection category
                $inspection_category->delete();
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
    public function updateCategoryInspectionStatus(Request $request, $id)
    {
        try {
            $inspection_category = InspectionProperties::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($inspection_category->trashed()) {
                    $inspection_category->restore();
    
                    // Log the status update activity for restore
                    $recordId = $inspection_category->id;
                    $changes = ['action' => 'Inspection Category restored'];
                    LoggingService::logActivity($request, 'restore', 'inspection_properties', $recordId, $changes);
                }
            } else {
                // Log the status update activity for soft delete
                $recordId = $inspection_category->id;
                $changes = ['action' => 'Inspection Category deleted'];
                LoggingService::logActivity($request, 'delete', 'inspection_properties', $recordId, $changes);
    
                // Soft delete the inspection category
                $inspection_category->delete();
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

}
