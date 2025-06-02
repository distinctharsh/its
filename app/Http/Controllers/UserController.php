<?php

namespace App\Http\Controllers;

use App\Models\Designation;
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
        $designations = Designation::all(); 
        return view('add-user', compact('roles', 'designations'));
    }

   public function createUser(Request $request)
    {
        try {
            // Captcha (Optional)
            // $captchaInput = $request->input('captcha');
            // $captchaSession = $request->session()->get('captcha_text');
            // if ($captchaInput !== $captchaSession) {
            //     return response()->json([
            //         'success' => false,
            //         'msg' => 'Captcha is not valid'
            //     ], 422);
            // }
            // $request->session()->forget('captcha_text');

            $validatedData = $request->validate([
                'user_name' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        $existingUser = User::all()->firstWhere(function ($user) use ($value) {
                            return $user->name === $value;
                        });
                        if ($existingUser) {
                            $fail('This username is already taken.');
                        }
                    }
                ],
                'user_email' => [
                    'required',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) {
                        $existingUser = User::all()->firstWhere(function ($user) use ($value) {
                            return $user->email === $value;
                        });
                        if ($existingUser) {
                            $fail('This email is already registered.');
                        }
                    }
                ],
                'user_role_id' => 'required|exists:roles,id',
                'designation_id' => 'required|exists:designations,id',
                'phone_number' => 'nullable|string|max:20',
            ], [
                'user_name.required' => 'User name is required',
                'user_email.required' => 'Email is required',
                'user_role_id.required' => 'User role is required',
                'user_role_id.exists' => 'Selected role does not exist',
                'designation_id.required' => 'Designation is required',
            ]);


            // Create user
            $user = User::create([
                'name' => $validatedData['user_name'],
                'email' => $validatedData['user_email'],
                'role_id' => $validatedData['user_role_id'],
                'designation_id' => $validatedData['designation_id'],
                'phone_number' => $validatedData['phone_number'] ?? null,
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
            dd($e); // Consider removing this in production
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
        $designations = Designation::all(); // Assuming you have a roles table

        return view('edit-user', compact('user', 'roles', 'designations'));
    }


    public function updateUser(Request $request, $id)
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


            // Validate user input
            $validatedData = $request->validate([
                'user_name' => [
                    'required',
                    'string',
                    'max:255',
                    function ($attribute, $value, $fail) use ($id) {
                        $existingUser = User::all()->firstWhere(function ($user) use ($value, $id) {
                            return $user->id != $id && $user->name === $value;
                        });
                        if ($existingUser) {
                            $fail('This username is already taken.');
                        }
                    }
                ],
                'user_email' => [
                    'required',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) use ($id) {
                        $existingUser = User::all()->firstWhere(function ($user) use ($value, $id) {
                            return $user->id != $id && $user->email === $value;
                        });
                        if ($existingUser) {
                            $fail('This email is already registered.');
                        }
                    }
                ],
                'user_password' => 'nullable|min:6',
                'user_role_id' => 'required|exists:roles,id',
                'designation_id' => 'required|exists:designations,id',
            ]);

            $user = User::findOrFail($id);

            $changes = [];

            // Compare each field and only update if changed
            if ($validatedData['user_name'] !== $user->name) {
                $user->name = $validatedData['user_name'];
                $changes['name'] = 'updated';
            }

            if ($validatedData['user_email'] !== $user->email) {
                $user->email = $validatedData['user_email'];
                $changes['email'] = 'updated';
            }

            if ($validatedData['user_role_id'] != $user->role_id) {
                $user->role_id = $validatedData['user_role_id'];
                $changes['role_id'] = 'updated';
            }

            if ($validatedData['designation_id'] != $user->designation_id) {
                $user->designation_id = $validatedData['designation_id'];
                $changes['designation_id'] = 'updated';
            }

            if ($request->filled('user_password')) {
                $user->password = Hash::make($request->user_password);
                $changes['password'] = 'updated';
            }

            // Only save if something changed
            if (!empty($changes)) {
                $user->save();
                LoggingService::logActivity($request, 'update', 'users', $id, $changes);
            }

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
