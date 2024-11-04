<?php

namespace App\Http\Controllers;

use App\Models\EscortOfficer;
use Illuminate\Http\Request;

class EscortOfficerController extends Controller
{
    //

    public function manageEscortOfficer()
    {
        $officers = EscortOfficer::withTrashed()->get();
        return view('manage-escort-officer', compact('officers'));
    }

    public function addEscortOfficer()
    {   
        return view('add-escort-officer');
    }

    public function createEscortOfficer(Request $request)
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
                'officer_name' => 'required|string|max:1000',
            ]);

            // Create a new EscortOfficer record
            $officer = EscortOfficer::create([
                'officer_name' => $validatedData['officer_name'],
            ]);

            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Escort Officer Created Successfully!'
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



    public function updateEscortOfficerStatus(Request $request, $id)
    {
        try {
            $officer = EscortOfficer::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($officer->trashed()) {
                    $officer->restore();
                    $officer->save();
                }
            } else {
                $officer->delete();
            }


            return response()->json([
                'success' => true,
                'msg' => 'Escort Officer Updated Successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }




    public function editEscortOfficer($id)
    {
        try {
            $officer = EscortOfficer::withTrashed()->findOrFail($id);
            return view('edit-officer', compact('officer'));
        } catch (\Exception $e) {
            \Log::error('Edit officer Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the officer.'], 500);
        }
    }

    public function updateEscortOfficer(Request $request, $id)
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
                'officer_name' => 'required|string|max:1000',
            ]);

            // Find the existing record
            $officer = EscortOfficer::withTrashed()->findOrFail($id);

            // Update the record
            $officer->update([
                'officer_name' => $validatedData['officer_name'],
            ]);

            // Clear the CAPTCHA session
            $request->session()->forget('captcha_text');

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

    public function deleteEscortOfficer(Request $request)
    {
        try {
            $status = EscortOfficer::withTrashed()->findOrFail($request->status_id);
            $status->save();
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



}
