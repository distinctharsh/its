<?php

namespace App\Http\Controllers;

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
                'rank_name' => 'required|string|max:1000',
            ]);

            // Create a new rank record
            $rank = Rank::create([
                'rank_name' => $validatedData['rank_name'],
            ]);

            $request->session()->forget('captcha_text');

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
                'rank_name' => 'required|string|max:1000',
            ]);

            // Find the existing record
            $rank = Rank::withTrashed()->findOrFail($id);

            // Update the record
            $rank->update([
                'rank_name' => $validatedData['rank_name'],
            ]);

            // Clear the CAPTCHA session
            $request->session()->forget('captcha_text');

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

            $rank->is_active = 0;
            $rank->save();

            $rank->delete();

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

            if ($isActive) {
                if ($rank->trashed()) {
                    $rank->restore();
                    $rank->save();
                }
            } else {
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
