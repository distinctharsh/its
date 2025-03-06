<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function manageUser(Request $request){
        $users = User::withTrashed()->get();
        $roles = User::pluck("name","id");
        return view("manage-user", compact("users", "roles"));
    }

    public function addUser()
    {
        $roles = Role::all();
        return view('add-user', compact('roles'));
    }

    public function createUser(Request $request)
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
            $request->session()->forget('captcha_text');
            $validatedData = $request->validate([
                'user_name' => 'required|string|max:255',
                'user_email' => 'required|email|unique:users,email|max:255',
                'user_role_id' => 'required|exists:roles,id',
            ], [
                'user_name.required' => 'User name is required',
                'user_email.required' => 'Email is required',
                'user_email.unique' => 'This email is already registered',
                'user_role_id.required' => 'User role is required',
                'user_role_id.exists' => 'Selected role does not exist',
            ]);

            $user = User::create([
                'name' => $validatedData['user_name'], 
                'email' => $validatedData['user_email'], 
                'role_id' => $validatedData['user_role_id'],
                'password' => Hash::make('12345678'), 
            ]);
            $recordId = $user->id;
            $changes = ['action' => 'New User added'];
            LoggingService::logActivity($request, 'insert', 'users', $recordId, $changes);

            return response()->json([
                'success' => true,
                'msg' => 'User Created Successfully! Default password: 12345678'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->validator->errors()->first() 
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all(); // Assuming you have a roles table

        return view('edit-user', compact('user', 'roles'));
    }


    public function updateUser(Request $request, $id)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'user_name' => 'required|string|max:255',
                'user_email' => 'required|email|unique:users,email,' . $id,
                'user_role_id' => 'required',
            ]);

            // Find the user
            $user = User::findOrFail($id);

            // Update user details
            $user->update([
                'name' => $validatedData['user_name'],
                'email' => $validatedData['user_email'],
                'role_id' => $validatedData['user_role_id'],
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'User Updated Successfully!'
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

    public function updateUserStatus(Request $request, $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $isActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);

            if ($isActive) {
                if ($user->trashed()) {
                    $user->restore();
                    $user->save();
                }
        
                // Log the status update activity for restore
                $changes = [
                    'action' => 'User restored' // Action type is 'restore' when the user is restored
                ];
                LoggingService::logActivity($request, 'restore', 'users', $user->id, $changes);
        
            } else {
                $user->delete();
        
                // Log the status update activity for soft delete
                $changes = [
                    'action' => 'User soft deleted' // Action type is 'deleted' when the user is soft deleted
                ];
                LoggingService::logActivity($request, 'delete', 'users', $user->id, $changes);
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
