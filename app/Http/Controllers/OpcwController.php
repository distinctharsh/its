<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\OpcwFax;
use App\Models\PageLock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpcwController extends Controller
{
    //

    public function manageOpcw()
    {
        $now = Carbon::now();
        $faxes = OpcwFax::withTrashed()->get();

         // Just fetch the lock for the page, no need to check current time
        $opcwLock = PageLock::where('page', 'opcw')
            ->where('locked', true)
            ->first();
        return view('manage-opcw', compact('faxes', 'opcwLock'));
    }
    

    public function addOpcw()
    {
        return view('add-opcw');
    }

    public function createOpcw(Request $request)
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
                'fax_date' => 'required|date',
                'fax_number' => 'required|string|max:255',
                'reference_number' => 'nullable|string|max:255',
                'fax_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
                'remarks' => 'nullable|string|max:500'
            ]);

            $faxDocumentPath = $request->hasFile('fax_document') ?
                $request->file('fax_document')->store('opcw_fax_documents') : null;

            // Create a new OPCW fax record
            $opcwFax = OpcwFax::create([
                'fax_date' => $validatedData['fax_date'],
                'fax_number' => $validatedData['fax_number'],
                'reference_number' => $validatedData['reference_number'] ? $validatedData['reference_number'] : '',
                'remarks' => $validatedData['remarks'] ?? null,
                'fax_document' => $faxDocumentPath,
            ]);



             // Prepare data to send in response
            $responseData = [
                'success' => true,
                'msg' => 'OPCW Fax Created Successfully!',
                'fax_number' => $opcwFax->fax_number,
                'fax_document' => $faxDocumentPath // The path to the fax document if it's uploaded
            ];

            $recordId = $opcwFax->id;
            $changes = ['action' => 'New opcw fax added'];
            LoggingService::logActivity($request, 'insert', 'opcw_faxes', $recordId, $changes);

            // $request->session()->forget('captcha_text');


            return response()->json($responseData);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
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
    public function editOpcw($id)
    {
        try {
            $fax = OpcwFax::withTrashed()->findOrFail($id); // Use withTrashed to include soft-deleted records
            return view('edit-opcw', compact('fax'));
        } catch (\Exception $e) {
            \Log::error('Edit OPCW Fax Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the OPCW fax.'], 500);
        }
    }


    public function updateOpcw(Request $request, $id)
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
            // Make the fields optional if not present in the update request
            $validatedData = $request->validate([
                'fax_date' => 'nullable|date',  // Allow fax_date to be null if it's not being updated
                'fax_number' => 'nullable|string|max:255', // Allow fax_number to be null if it's not being updated
                'reference_number' => 'nullable|string|max:255', // Allow reference_number to be null if it's not being updated
                'remarks' => 'nullable|string|max:500',
                'fax_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            ]);

            // Find the existing record
            $opcwFax = OpcwFax::withTrashed()->findOrFail($id);

            // Capture the original data for logging
            $originalData = $opcwFax->only(['fax_date', 'fax_number', 'reference_number', 'remarks', 'fax_document']);

            // Track whether each field has been modified
            $updatedData = [];

            // Check if the fax_date has changed
            if ($request->input('fax_date') !== null && $request->input('fax_date') !== $opcwFax->fax_date) {
                $updatedData['fax_date'] = $request->input('fax_date');
            }

            // Check if the fax_number has changed
            if ($request->input('fax_number') !== null && $request->input('fax_number') !== $opcwFax->fax_number) {
                $updatedData['fax_number'] = $request->input('fax_number');
            }

            // Check if the reference_number has changed
            if ($request->input('reference_number') !== null && $request->input('reference_number') !== $opcwFax->reference_number) {
                $updatedData['reference_number'] = $request->input('reference_number');
            }

            // Check if the remarks have changed
            if ($request->input('remarks') !== null && $request->input('remarks') !== $opcwFax->remarks) {
                $updatedData['remarks'] = $request->input('remarks');
            }

            // Handle file upload (only update if a new file has been uploaded)
            if ($request->hasFile('fax_document')) {
                $faxDocumentPath = $request->file('fax_document')->store('opcw_fax_documents');
                $updatedData['fax_document'] = $faxDocumentPath;
            } elseif ($request->input('fax_document') === null) {
                // If no new file is uploaded, retain the existing document
                $updatedData['fax_document'] = $opcwFax->fax_document;
            }

            // If there are any changes, update the record
            if (count($updatedData) > 0) {
                $opcwFax->update($updatedData);

                // Log the changes
                $changes = [
                    'old_data' => $originalData,
                    'new_data' => $updatedData
                ];
                LoggingService::logActivity($request, 'update', 'opcw_faxes', $opcwFax->id, $changes);

                // Clear the CAPTCHA session
                // $request->session()->forget('captcha_text');

                return response()->json([
                    'success' => true,
                    'msg' => 'OPCW Fax Updated Successfully!'
                ]);
            } else {
                // If no changes were detected
                return response()->json([
                    'success' => false,
                    'msg' => 'No changes detected to update.'
                ], 400);
            }
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





    public function deleteOpcw(Request $request)
    {
        try {
            $fax = OpcwFax::withTrashed()->findOrFail($request->fax_id); // Ensure we find even soft deleted entries

            $recordId = $fax->id;
            $changes = ['action' => 'OPCW Fax deleted'];
            LoggingService::logActivity($request, 'delete', 'opcw_faxes', $recordId, $changes);

            $fax->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'msg' => 'OPCW Fax deleted successfully!'
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


    public function updateOpcwStatus(Request $request, $id)
    {
        try {
            $fax = OpcwFax::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
    
            $recordId = $fax->id;
    
            // Log the activity based on the isActive status
            if ($isActive) {
                // Restore the fax if trashed
                if ($fax->trashed()) {
                    $fax->restore();
                    $changes = ['action' => 'OPCW Fax restored'];
                    LoggingService::logActivity($request, 'restore', 'opcw_faxes', $recordId, $changes);
                }
            } else {
                // Soft delete the fax if not trashed
                if (!$fax->trashed()) {
                    $fax->delete();
                    $changes = ['action' => 'OPCW Fax deleted'];
                    LoggingService::logActivity($request, 'delete', 'opcw_faxes', $recordId, $changes);
                }
            }
    
            // Save the changes
            $fax->save();
    
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
