<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
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

            $recordId = $site_code->id;
            $changes = ['action' =>'New Site Code added'];
            LoggingService::logActivity($request, 'insert', 'site_codes', $recordId, $changes);

            // $request->session()->forget('captcha_text');

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
                'site_name' => 'required|string|max:1000',
                'site_code' => 'required',
                'site_address' => 'required',
                'state_id' => 'required',
            ]);

            // Find the existing record
            $site_code = SiteCode::withTrashed()->findOrFail($id);

            // Capture the original values for logging
            $originalData = [
                'site_name' => $site_code->site_name,
                'site_code' => $site_code->site_code,
                'site_address' => $site_code->site_address,
                'state_id' => $site_code->state_id,
            ];

            // Update the record
            $site_code->update([
                'site_name' => $validatedData['site_name'],
                'site_code' => $validatedData['site_code'],
                'site_address' => $validatedData['site_address'],
                'state_id' => $validatedData['state_id'],
            ]);

            // Log the changes
            $changes = [
                'old_data' => $originalData,
                'new_data' => [
                    'site_name' => $validatedData['site_name'],
                    'site_code' => $validatedData['site_code'],
                    'site_address' => $validatedData['site_address'],
                    'state_id' => $validatedData['state_id'],
                ],
            ];
            LoggingService::logActivity($request, 'update', 'site_codes', $site_code->id, $changes);

            // Clear the CAPTCHA session
            // $request->session()->forget('captcha_text');

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

            $changes = ['action' => 'Site Code deleted'];
            LoggingService::logActivity($request, 'delete', 'site_codes', $site_code->id, $changes);

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
                    
                    // Log activity for restoring the site code
                    $changes = ['action' => 'Site Code restored'];
                    LoggingService::logActivity($request, 'restore', 'site_codes', $site_code->id, $changes);
                }
            } else {
                // Log activity for soft deleting the site code
                $changes = ['action' => 'Site Code deleted'];
                LoggingService::logActivity($request, 'delete', 'site_codes', $site_code->id, $changes);
                
                // Soft delete the site code
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
