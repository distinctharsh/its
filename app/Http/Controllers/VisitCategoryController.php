<?php

namespace App\Http\Controllers;

use App\Models\VisitCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitCategoryController extends Controller
{
    //

    public function manageVisitCategory()
    {
        $user = Auth::user();

        $visit_categories = VisitCategory::withTrashed()->get();

        return view('manage-visit-category', compact('visit_categories'));
    }

    public function addVisitCategory()
    {
        return view('add-visit-category');
    }

    public function createVisitCategory(Request $request)
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

            // Create a new visit categories record
            $visit_category = VisitCategory::create([
                'category_name' => $validatedData['category_name'],
            ]);

            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'visit Categories Created Successfully!'
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

    public function editVisitCategory($id)
    {
        try {
            $visit_category = VisitCategory::withTrashed()->findOrFail($id);
            return view('edit-visit-category', compact('visit_category'));
        } catch (\Exception $e) {
            \Log::error('Edit visit Categories Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the visit category.'], 500);
        }
    }

    public function updateVisitCategory(Request $request, $id)
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

            $visit_category = VisitCategory::withTrashed()->findOrFail($id);


            $visit_category->update([
                'category_name' => $validatedData['category_name'],
            ]);


            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'visit Category Updated Successfully!'
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

    public function deleteVisitCategory(Request $request)
    {
        try {
            $visit_category = VisitCategory::withTrashed()->findOrFail($request->visit_category_id);

            // Perform soft delete
            $visit_category->delete();

            return response()->json([
                'success' => true,
                'msg' => 'visit Category deleted successfully!'
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


    public function updateVisitCategoryStatus(Request $request, $id)
    {
        try {
            $visit_category = VisitCategory::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($visit_category->trashed()) {
                    $visit_category->restore(); 
                }
            } else {
                $visit_category->delete(); 
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