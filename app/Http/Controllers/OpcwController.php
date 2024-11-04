<?php

namespace App\Http\Controllers;

use App\Models\OpcwFax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpcwController extends Controller
{
    //

    public function manageOpcw()
    {
        $user = Auth::user();
        $faxes = OpcwFax::whereNull('deleted_at')->get();

        if ($user->hasRole('Admin')) {
            $faxes = OpcwFax::withTrashed()->get();
        } else {
            $faxes = OpcwFax::withTrashed()->get();
            // $faxes = OpcwFax::whereNull('deleted_at')->where('is_active', operator: 1)->get();
        }
        return view('manage-opcw', compact('faxes'));
    }

    public function addOpcw()
    {
        return view('add-opcw');
    }

    public function createOpcw(Request $request)
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
                'fax_date' => 'required|date',
                'fax_number' => 'required|string|max:255',
                'reference_number' => 'required|string|max:255',
                'fax_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
                'remarks' => 'nullable|string|max:500'
            ]);

            $faxDocumentPath = $request->hasFile('fax_document') ?
                $request->file('fax_document')->store('opcw_fax_documents') : null;

            // Create a new OPCW fax record
            $opcwFax = OpcwFax::create([
                'fax_date' => $validatedData['fax_date'],
                'fax_number' => $validatedData['fax_number'],
                'reference_number' => $validatedData['reference_number'],
                'remarks' => $validatedData['remarks'] ?? null,
                'fax_document' => $faxDocumentPath,
            ]);

            $request->session()->forget('captcha_text');


            return response()->json([
                'success' => true,
                'msg' => 'OPCW Fax Created Successfully!'
            ]);
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
                'fax_date' => 'required|date',
                'fax_number' => 'required|string|max:255',
                'reference_number' => 'required|string|max:255',
                'remarks' => 'nullable|string|max:500',
                'fax_document' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:5120',
            ]);

            // Find the existing record
            $opcwFax = OpcwFax::withTrashed()->findOrFail($id);

            $faxDocumentPath = $request->hasFile('fax_document')
            ? $request->file('fax_document')->store('opcw_fax_documents')
            : $opcwFax->fax_document;


            // Update the record
            $opcwFax->update([
                'fax_date' => $validatedData['fax_date'],
                'fax_number' => $validatedData['fax_number'],
                'reference_number' => $validatedData['reference_number'],
                'remarks' => $validatedData['remarks'] ?? null, 
                'fax_document' => $faxDocumentPath, // Handle optional fields
            ]);

            // Clear the CAPTCHA session
            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'OPCW Fax Updated Successfully!'
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



    public function deleteOpcw(Request $request)
    {
        try {
            $fax = OpcwFax::withTrashed()->findOrFail($request->fax_id); // Ensure we find even soft deleted entries
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

            if ($isActive) {
                if ($fax->trashed()) {
                    $fax->restore();
                }
            } else {
                if (!$fax->trashed()) {
                    $fax->delete(); // Soft delete if deactivating
                }
            }

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
