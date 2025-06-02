<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use App\Models\Inspector;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function loadLogin()
    {
        return view('login');
    }

    public function userLogin(Request $request)
    {
        $userCredentials = $request->only(['email', 'password']);
        $captchaInput = $request->input('captcha');
        $captchaSession = $request->session()->get('captcha_text');

        if ($captchaInput !== $captchaSession) {
            $this->logLoginAttempt($request, 'failed', 'Captcha validation failed');
            return back()->with('error', 'Captcha is not valid');
        }

        if (Auth::attempt($userCredentials)) {
            $request->session()->forget('captcha_text');
            $this->logLoginAttempt($request, 'success', 'Login successful');
            return redirect('/dashboard');
        }

        $this->logLoginAttempt($request, 'failed', 'Email & Password is incorrect');
        return back()->with('error', 'Email & Password is incorrect');
    }




    public function dashboard()
    {
        $user = Auth::user();

        // Initialize counts
        $inspectorCount = 0;
        $inspectionCount = 0;
        $visitCount = 0;
        $opcwFaxeCount = 0;
        $reportCount = 0;

        if ($user->hasRole('Admin')) {
            $inspectorCount = DB::table('inspectors')->whereNull('deleted_at')->count();
            $inspectionCount = DB::table('inspections')->whereNull('deleted_at')->count();
            $visitCount = DB::table('visits')->whereNull('deleted_at')->count();
            $opcwFaxeCount = DB::table('opcw_faxes')->whereNull('deleted_at')->count();
            $inspectionData = DB::table('visits')
                                ->where('inspection_category_type_id', 2) // Filter by inspection_category_type_id
                                ->whereNull('deleted_at') // Check if deleted_at is NULL
                                ->select('inspection_category_type_id', DB::raw('count(inspection_category_type_id) as count_cti'))
                                ->groupBy('inspection_category_type_id')
                                ->first(); // Retrieve the first result

        } else {
            $inspectorCount = DB::table('inspectors')->whereNull('deleted_at')->count();
            $inspectionCount = DB::table('inspections')->whereNull('deleted_at')->count();
            $visitCount = DB::table('visits')->whereNull('deleted_at')->count();
            $opcwFaxeCount = DB::table('opcw_faxes')->whereNull('deleted_at')->count();
            $inspectionData = DB::table('visits')
                                ->where('inspection_category_type_id', 2) // Filter by inspection_category_type_id
                                ->whereNull('deleted_at') // Check if deleted_at is NULL
                                ->select('inspection_category_type_id', DB::raw('count(inspection_category_type_id) as total'))
                                ->groupBy('inspection_category_type_id')
                                ->first(); // Retrieve the first result
        }


        return view('dashboard', [
            'inspectorCount' => $inspectorCount,
            'inspectionCount' => $inspectionCount,
            'visitCount' => $visitCount,
            'opcwFaxeCount' => $opcwFaxeCount,
            'reportCount' => $reportCount,
            'SequentialInspection' => $inspectionData->total ?? 0,
            'id' => $inspectionData->inspection_category_type_id ?? null
        ]);
    }

    public function logout(Request $request)
    {
        try {
            // Log out the user and flush session data
            $this->logLoginAttempt($request, 'success', 'User logged out successfully');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // If the request is AJAX (expects JSON response)
            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            // For regular HTTP requests, redirect to the login page
            return redirect('/')->with('success', 'You have been logged out successfully.');
        } catch (\Exception $e) {
            $this->logLoginAttempt($request, 'failed', 'Logout failed: ' . $e->getMessage());
            // Handle any exceptions during logout
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'msg' => $e->getMessage()]);
            }

            // Redirect back with an error message for normal requests
            return redirect()->back()->withErrors(['error' => 'Logout failed: ' . $e->getMessage()]);
        }
    }


    public function loadRegister()
    {
        return view('register');
    }

    public function userRegister(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required',
                'email' => 'required|unique:users|max:255',
                'password' => 'required|min:8',
                'inspector_name' => 'required',
                'passport_number' => 'required',
            ]);

            $inspector = Inspector::create([
                'name' => $request->inspector_name,
                'passport_number' => $request->passport_number,
                // Add other inspector fields here
            ]);

            $recordId = $inspector->id;
            $changes = ['action' => 'New inspector added'];
            LoggingService::logActivity($request, 'insert', 'inspectors', $recordId, $changes);

            $role = Role::where('name', 'User')->first();

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'inspector_id' => $inspector->id,
                'role_id' => $role ? $role->id : 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'inspector_name' => $request->inspector_name,
                'passport_number' => $request->passport_number,
            ]);

            $recordId = $user->id;
            $changes = ['action' => 'New users added'];
            LoggingService::logActivity($request, 'insert', 'users', $recordId, $changes);

            return back()->with('success', 'Register Successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function logLoginAttempt(Request $request, string $status, string $details)
    {
        $user = Auth::user();
        $username = $user ? $user->email : 'Guest';
        $userId = $user ? $user->id : null;

        AuditTrail::create([
            'user_id' => $userId,
            'username' => $username,
            'ip_addr' => $request->ip(),
            'status' => $status,
            'action_details' => $details,
            'created_at' => now(),
        ]);
    }


    // Show Change Password Form
    public function showChangePasswordForm()
    {
        return view('auth.change-password'); // The view where the password change form will be shown
    }

    // Handle Change Password Logic
    public function changePassword(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed', // Minimum length and confirmation
        ]);

        $user = auth()->user(); // Get the currently authenticated user

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The current password is incorrect.'
            ], 400); // Return error with appropriate status
        }

        // Update the user's password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Log the user out after successful password change
        auth()->logout();

        // Return success response
        return response()->json([
            'status' => 'success',
            'message' => 'Your password has been changed successfully.'
        ]);
    }
}
