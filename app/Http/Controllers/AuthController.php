<?php

namespace App\Http\Controllers;

use App\Models\Inspector;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            return back()->with('error', 'Captcha is not valid');
        }

        if (Auth::attempt($userCredentials)) {
            $request->session()->forget('captcha_text');
            return redirect('/dashboard');
        }

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
            $reportCount = DB::table('reports')->whereNull('deleted_at')->count();
        } else {
            $inspectorCount = DB::table('inspectors')->whereNull('deleted_at')->count();
            $inspectionCount = DB::table('inspections')->whereNull('deleted_at')->count();
            $visitCount = DB::table('visits')->whereNull('deleted_at')->count();
            $opcwFaxeCount = DB::table('opcw_faxes')->whereNull('deleted_at')->count();
            $reportCount = DB::table('reports')->whereNull('deleted_at')->count();
        }


        return view('dashboard', [
            'inspectorCount' => $inspectorCount,
            'inspectionCount' => $inspectionCount,
            'visitCount' => $visitCount,
            'opcwFaxeCount' => $opcwFaxeCount,
            'reportCount' => $reportCount
        ]);
    }


    public function logout(Request $request)
    {
        try {
            // Log out the user and flush session data
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // If the request is AJAX (expects JSON response)
            if ($request->wantsJson()) {
                return response()->json(['success' => true]);
            }

            // For regular HTTP requests, redirect to the login page
            return redirect()->route('login')->with('success', 'You have been logged out successfully.');
        } catch (\Exception $e) {
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

            $role = Role::where('name', 'User')->first();
            User::insert([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'inspector_id' => $inspector->id,
                'role_id' => $role ? $role->id : 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isnpector_name' => $request->inspector_name,
                'passport_number' => $request->passport_number,
            ]);

            return back()->with('success', 'Register Successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
