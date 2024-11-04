<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StateController extends Controller
{
    //
    public function manageState()
    {
        $states = State::withTrashed()->get();
        return view('manage-states', compact('states'));
    }

    public function addState()
    {
        return view('add-state');
    }

    public function createState(Request $request)
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
                'state_name' => 'required|string|max:255',
            ]);

            // Create a new state record
            $state = State::create([
                'state_name' => $validatedData['state_name'],
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'State Created Successfully!'
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

    public function editState($id)
    {
        try {
            $state = State::withTrashed()->findOrFail($id);
            return view('edit-state', compact('state'));
        } catch (\Exception $e) {
            \Log::error('Edit State Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the state.'], 500);
        }
    }

    public function updateState(Request $request, $id)
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
                'state_name' => 'required|string|max:255',
            ]);

            // Find the existing record
            $state = State::withTrashed()->findOrFail($id);

            // Update the record
            $state->update([
                'state_name' => $validatedData['state_name'],
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'State Updated Successfully!'
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

    public function deleteState(Request $request)
    {
        try {
            $state = State::withTrashed()->findOrFail($request->nationality_id);
            $state->save();
            $state->delete(); 

            return response()->json([
                'success' => true,
                'msg' => 'State deleted and status updated!'
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

    public function updateStateStatus(Request $request, $id)
    {
        try {
            $state = State::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($state->trashed()) {
                    $state->restore();
                    $state->save();
                }
               
            } else {
                $state->delete();
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
