<?php

namespace App\Http\Controllers;

use App\Models\InspectionCategory;
use App\Models\InspectionCategoryType;
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

    public function addInspectionCategory()
    {
        $inspection_types = InspectionCategoryType::all();
        return view('add-inspection-category', compact('inspection_types'));
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


            $inspection_category->update([
                'category_name' => $validatedData['category_name'],
            ]);

            $inspection_category->types()->sync($validatedData['inspection_types']);


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
                }
            } else {
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
