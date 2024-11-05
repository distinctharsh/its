<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use Illuminate\Http\Request;
use App\Models\Inspection;
use App\Models\InspectionCategory;
use App\Models\Inspector;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{

    public function manageInspection()
    {
        $user = Auth::user();
    
        $inspections = Inspection::with([
            'category' => function ($query) {
                $query->withTrashed();
            },
            'status',
            'inspector' 
        ])->withTrashed()->get();
    
        return view('manage-inspection', compact('inspections'));
    }
    

    public function show($id)
    {
        // Retrieve the inspection with related models
        $inspection = Inspection::with(['category' => function ($query) {
            $query->withTrashed();
        }, 'status', 'inspector'])->withTrashed()->find($id);

        if (!$inspection) {
            abort(404);
        }

        return view('inspection-details', compact('inspection'));
    }


    public function addInspection()
    {
        // Get only active inspectors

        $inspectors = Inspector::whereNull('deleted_at')->get();
        $inspection_categories = InspectionCategory::whereNull('deleted_at')->get();
        $statuses = Status::whereNull('deleted_at')->get();

        return view('add-inspection', compact('inspectors', 'inspection_categories', 'statuses'));
    }

    public function createInspection(Request $request)
    {
        try {

            $user = Auth::user();


            $captchaInput = $request->input('captcha');
            $captchaSession = $request->session()->get('captcha_text');

            if ($captchaInput !== $captchaSession) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Captcha is not valid'
                ], 422);
            }




            // Validate the input fields
            $validatedData = $request->validate([
                'inspector_id' => 'required|exists:inspectors,id',
                'category_id' => 'required|exists:inspection_categories,id',
                'date_of_joining' => 'required|date',
                'status_id' => 'required|exists:statuses,id',
                'remarks' => 'nullable|string|max:1000',
            ]);

            // Create a new inspection record
            $inspection = Inspection::create([
                'inspector_id' => $validatedData['inspector_id'],
                'category_id' => $validatedData['category_id'],
                'date_of_joining' => $validatedData['date_of_joining'],
                'status_id' => $validatedData['status_id'],
                'remarks' => $validatedData['remarks'],
                'created_by' => $user->id,
            ]);

            $insertId = $inspection->id;
            $changes = ['action' =>'New Inspection added'];

            LoggingService::logActivity($request, 'insert', 'inspections', $insertId, $changes);

            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Inspection Created!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'msg' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // General errors
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function editInspection($id)
    {
        try {
            $inspection = Inspection::withTrashed()->with(['category' => function ($query) {
                $query->withTrashed();
            }, 'status'])->findOrFail($id);
            $inspectors = Inspector::all();
            $inspection_categories = InspectionCategory::withTrashed()->get();
            $statuses = Status::all();

            return view('edit-inspection', compact('inspection', 'inspectors', 'inspection_categories', 'statuses'));
        } catch (\Exception $e) {
            \Log::error('Edit Inspection Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the inspection.'], 500);
        }
    }


    public function updateInspection(Request $request, $id)
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

            // Validate the request
            $validated = $request->validate([
                'inspector_id' => 'required|integer|exists:inspectors,id',
                'category_id' => 'required|integer|exists:inspection_categories,id',
                'date_of_joining' => 'required|date',
                'status_id' => 'required|integer|exists:statuses,id',
                'remarks' => 'nullable|string',
            ]);

            $inspection = Inspection::withTrashed()->findOrFail($id);

            $originalData = $inspection->only(['inspector_id', 'category_id', 'date_of_joining', 'status_id', 'remarks']);

            $inspection->update($validated);

            $changes = [
                'old_data' => $originalData,
                'new_data' => $validated
            ];
            LoggingService::logActivity($request, 'update', 'inspections', $inspection->id, $changes);

            // Clear the CAPTCHA session
            $request->session()->forget('captcha_text');
            return response()->json(['success' => true, 'msg' => 'Inspection updated successfully.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'msg' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // General errors
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }




    public function deleteInspection(Request $request)
    {
        try {
            $inspection = Inspection::withTrashed()->findOrFail($request->inspection_id);

            // Only delete if it's not already trashed
            if (!$inspection->trashed()) {
                $inspection->delete();
                return response()->json([
                    'success' => true,
                    'msg' => 'Inspection deleted successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => 'Inspection is already deleted!'
                ], 422);
            }
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

    public function updateInspectionStatus(Request $request, $id)
    {
        try {
            $inspection = Inspection::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($inspection->trashed()) {
                    $inspection->restore();

                    $recordId = $inspection->id;
                    $changes = ['action' => 'Inspection restored'];
                    LoggingService::logActivity($request, 'update', 'inspections', $recordId, $changes);
                }
            } else {
                if (!$inspection->trashed()) {
                    $inspection->delete();
                    $recordId = $inspection->id;
                    $changes = ['action' => 'Inspection deleted'];
                    LoggingService::logActivity($request, 'delete', 'inspections', $recordId, $changes);
                }
            }

            $inspection->save();

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
