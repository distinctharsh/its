<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankController extends Controller
{
    public function manageRank()
    {
        $user = Auth::user();
        $ranks = Rank::withTrashed()->get();
        return view('manage-rank', compact('ranks'));
    }

    public function addRank()
    {
        return view('add-rank');
    }

    public function createRank(Request $request)
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
                'rank_name' => 'required|string|max:1000',
            ]);

            // Create a new rank record
            $rank = Rank::create([
                'rank_name' => $validatedData['rank_name'],
            ]);

            $recordId = $rank->id;
            $changes = ['action' => 'New Rank added'];
            LoggingService::logActivity($request, 'insert', 'ranks', $recordId, $changes);

            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Rank Created Successfully!'
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

    public function editRank($id)
    {
        try {
            $rank = Rank::withTrashed()->findOrFail($id);
            return view('edit-rank', compact('rank'));
        } catch (\Exception $e) {
            \Log::error('Edit Rank Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => 'An error occurred while retrieving the rank.'], 500);
        }
    }

    public function updateRank(Request $request, $id)
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
                'rank_name' => 'required|string|max:1000',
            ]);

            // Find the existing record
            $rank = Rank::withTrashed()->findOrFail($id);

            // Capture the original rank name for logging
            $originalRankName = $rank->rank_name;

            // Update the record
            $rank->update([
                'rank_name' => $validatedData['rank_name'],
            ]);

            // Log the changes
            $changes = [
                'old_data' => [
                    'rank_name' => $originalRankName,
                ],
                'new_data' => [
                    'rank_name' => $validatedData['rank_name'],
                ]
            ];
            LoggingService::logActivity($request, 'update', 'ranks', $rank->id, $changes);

            // Clear the CAPTCHA session
            // $request->session()->forget('captcha_text');

            return response()->json([
                'success' => true,
                'msg' => 'Rank Updated Successfully!'
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

    public function deleteRank(Request $request)
    {
        try {
            $rank = Rank::withTrashed()->findOrFail($request->rank_id);

            $changes = ['action' => 'Rank status updated to inactive'];
            LoggingService::logActivity($request, 'update', 'ranks', $rank->id, $changes);

          
            $rank->save();

            $rank->delete();

            $changes = ['action' => 'Rank deleted'];
            LoggingService::logActivity($request, 'delete', 'ranks', $rank->id, $changes);

            return response()->json([
                'success' => true,
                'msg' => 'Rank deleted and status updated!'
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

    public function updateRankStatus(Request $request, $id)
    {
        try {
            $rank = Rank::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            // Handle restoring or soft deleting the rank
            if ($isActive) {
                if ($rank->trashed()) {
                    $rank->restore();
                    $rank->save();

                    // Log the status update activity for restore
                    $changes = ['action' => 'Rank restored'];
                    LoggingService::logActivity($request, 'restore', 'rank', $rank->id, $changes);
                }
            } else {
                // Log the status update activity for soft delete
                $changes = ['action' => 'Rank soft deleted'];
                LoggingService::logActivity($request, 'delete', 'rank', $rank->id, $changes);

                // Soft delete the rank
                $rank->delete();
            }

            $rank->save();

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
