<?php

namespace App\Http\Controllers;

use App\Models\Nationality;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NationalityController extends Controller
{
    public function manageNationality()
    {
        $user = Auth::user();
        $nationalities = Nationality::whereNull('deleted_at')->get();

        if ($user->hasRole('Admin')) {
            $nationalities = Nationality::withTrashed()->get();
        } else {
            $nationalities = Nationality::whereNull('deleted_at')->where('is_active', 1)->get();
        }
        return view('manage-nationalities', compact('nationalities'));
    }

    public function addNationality()
    {
        return view('add-nationality');
    }

    public function createNationality(Request $request)
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
                'country_name' => 'required|string|max:255',
            ]);

            // Create a new nationality record
            $nationality = Nationality::create([
                'country_name' => $validatedData['country_name'],
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Nationality Created Successfully!'
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

    public function editNationality($id)
    {
        try {
            $nationality = Nationality::withTrashed()->findOrFail($id);
            return view('edit-nationality', compact('nationality'));
        } catch (\Exception $e) {
            \Log::error('Edit Nationality Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the nationality.'], 500);
        }
    }

    public function updateNationality(Request $request, $id)
    {
        try {

            // CAPTCHA validation
            $captchaInput = $request->input('captcha');
            $captchaSession = $request->session()->get('captcha_text');

            if ($captchaInput !== $captchaSession) {
                return response()->json(['success' => false, 'msg' => 'Captcha is not valid'], 422);
            }

            
            // Validate the incoming request
            $validatedData = $request->validate([
                'country_name' => 'required|string|max:255',
            ]);

            // Find the existing record
            $nationality = Nationality::withTrashed()->findOrFail($id);

            // Update the record
            $nationality->update([
                'country_name' => $validatedData['country_name'],
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Nationality Updated Successfully!'
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

    public function deleteNationality(Request $request)
    {
        try {
            $nationality = Nationality::withTrashed()->findOrFail($request->nationality_id);
            $nationality->save();
            $nationality->delete(); 

            return response()->json([
                'success' => true,
                'msg' => 'Nationality deleted and status updated!'
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

    public function updateNationalityStatus(Request $request, $id)
    {
        try {
            $nationality = Nationality::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($nationality->trashed()) {
                    $nationality->restore();
                    $nationality->save();
                }
               
            } else {
                $nationality->delete();
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
