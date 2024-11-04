<?php

namespace App\Http\Controllers;

use App\Models\SiteCode;
use App\Models\State;
use Illuminate\Http\Request;

class SiteCodeController extends Controller
{
    public function manageSiteCode()
{
    $siteCodes = SiteCode::withTrashed()->with('state')->get();
    return view('manage-site-code', compact('siteCodes'));
}



    public function addSiteCode()
    {
        $states = State::whereNull('deleted_at')->get();
        return view('add-site-code', compact('states'));
    }

    public function createSiteCode(Request $request)
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
                'site_name' => 'required|string|max:1000',
                'site_code' => 'required',
                'site_address' => 'required',
                'state_id' => 'required',
            ]);

            // Create a new site record
            $site_code = SiteCode::create([
                'site_name' => $validatedData['site_name'],
                'site_code' => $validatedData['site_code'],
                'site_address' => $validatedData['site_address'],
                'state_id' => $validatedData['state_id'],
            ]);

            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Site Created Successfully!'
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

    public function editSiteCode($id)
    {
        try {
            $states = State::whereNull('deleted_at')->get();
            $siteCode = SiteCode::withTrashed()->findOrFail($id);
            return view('edit-site-code', compact('siteCode', 'states'));
        } catch (\Exception $e) {
            \Log::error('Edit SiteCode Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the SiteCode.'], 500);
        }
    }

    public function updateSiteCode(Request $request, $id)
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
                'site_name' => 'required|string|max:1000',
                'site_code' => 'required',
                'site_address' => 'required',
                'state_id' => 'required',
            ]);

            // Find the existing record
            $site_code = SiteCode::withTrashed()->findOrFail($id);

            // Update the record
            $site_code->update([
                'site_name' => $validatedData['site_name'],
                'site_code' => $validatedData['site_code'],
                'site_address' => $validatedData['site_address'],
                'state_id' => $validatedData['state_id'],
            ]);

            // Clear the CAPTCHA session
            $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'SiteCode Updated Successfully!'
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

    public function deleteSiteCode(Request $request)
    {
        try {
            $site_code = SiteCode::withTrashed()->findOrFail($request->site_id);
            $site_code->save();
            $site_code->delete();

            return response()->json([
                'success' => true,
                'msg' => 'SiteCode Deleted!'
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

    public function updateSiteCodeStatus(Request $request, $id)
    {
        try {
            $site_code = SiteCode::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($site_code->trashed()) {
                    $site_code->restore();
                    $site_code->save();
                }
            } else {
                $site_code->delete();
            }


            return response()->json([
                'success' => true,
                'msg' => 'SiteCode Updated Successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
