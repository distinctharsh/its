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
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\Crypt;
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

        //Uncomment for Black Box
        //     $captchaInput = $request->input('captcha');
        // $captchaSession = $request->session()->get('captcha_text');

        // if ($captchaInput !== $captchaSession) {
        //     $this->logLoginAttempt($request, 'failed', 'Captcha validation failed');
        //     return back()->with('error', 'Captcha is not valid');
        // }

        $username = $request->input('username');
        $password = $request->input('password');

        // Find user by decrypted name
        $user = User::all()->firstWhere(function ($user) use ($username) {
            return $user->name === $username;
        });

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            // $request->session()->forget('captcha_text');
            $this->logLoginAttempt($request, 'success', 'Login successful');
            return redirect('/dashboard');
        }

        $this->logLoginAttempt($request, 'failed', 'Username & Password is incorrect');
        return back()->with('error', 'Username & Password is incorrect');
    }






    public function dashboard()
    {
        $user = Auth::user();
        // Initialize counts
        $inspectorCount = 0;
        $inspectionCount = 0;
        $visitCount = 0;
        $opcwFaxeCount = 0;
        $inspectorCount = DB::table('inspectors')->whereNull('deleted_at')->count();
        //$inspectionCount = DB::table('inspections')->whereNull('deleted_at')->count();
        $visitCount = DB::table('visits')
                            ->join('visit_site_mappings', 'visit_site_mappings.visit_id', '=', 'visits.id')
                            ->whereNull('visits.deleted_at')->count();
        $opcwFaxeCount = DB::table('opcw_faxes')->whereNull('deleted_at')->count();
        $inspectionData = DB::table('visits')
        ->join('visit_site_mappings', 'visit_site_mappings.visit_id', '=', 'visits.id')
        ->join('inspection_category_types', 'inspection_category_types.id', '=', 'visits.inspection_category_type_id')
        ->where('inspection_category_types.id', 1)
        ->whereNull('visits.deleted_at')
        ->groupBy('visits.inspection_category_type_id')
        ->select('visits.inspection_category_type_id', DB::raw('COUNT(*) as total'))
        ->first();
        $sequentialInspectionData = DB::table('visits')
        ->join('visit_site_mappings', 'visit_site_mappings.visit_id', '=', 'visits.id')
        ->join('inspection_category_types', 'inspection_category_types.id', '=', 'visits.inspection_category_type_id')
        ->selectRaw('YEAR(visits.arrival_datetime) as year, COUNT(visits.id) as total')
        ->where('inspection_category_types.id', 1) // Sequential Inspection category
        ->whereNull('visits.deleted_at')
        ->groupBy(DB::raw('YEAR(visits.arrival_datetime)'))
        ->orderBy('year', 'ASC')
        ->get();
        $nonSequentialInspectionData = DB::table('visits')
        ->join('visit_site_mappings', 'visit_site_mappings.visit_id', '=', 'visits.id')
        ->join('inspection_category_types', 'inspection_category_types.id', '=', 'visits.inspection_category_type_id')
        ->selectRaw('YEAR(visits.arrival_datetime) as year, COUNT(visits.id) as total')
        ->where('inspection_category_types.id', 2) // Sequential Inspection category
        ->whereNull('visits.deleted_at')
        ->groupBy(DB::raw('YEAR(visits.arrival_datetime)'))
        ->orderBy('year', 'ASC')
        ->get();
    
        $currentYear = date('Y');

        // Fetch distinct years from visits table (last 5 years)
        $years = DB::table('visits')
        ->selectRaw('YEAR(arrival_datetime) as year')
        ->groupBy(DB::raw('YEAR(arrival_datetime)'))
        ->orderBy('year', 'ASC')
        ->whereRaw('YEAR(arrival_datetime) >= ?', [$currentYear - 5])
        ->get();

        // Fetch year-wise visits grouped by year, state, and inspection types
        $visits = DB::table('visits') 
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
            ->join('states', 'visit_site_mappings.state_id', '=', 'states.id')
            ->selectRaw("
                YEAR(visits.arrival_datetime) as year,
                visit_site_mappings.state_id,
                states.state_name,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END), 0) as schedule_1,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END), 0) as schedule_2,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END), 0) as schedule_3,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END), 0) as schedule_4
            ")
            ->groupBy(DB::raw("YEAR(visits.arrival_datetime), visit_site_mappings.state_id, states.state_name"))
            ->whereNotNull('visits.arrival_datetime')
            ->whereNull('visits.deleted_at')
            ->orderBy('year', 'ASC')
            ->get();

        //Pie Chart
        // Fetch year-wise visits grouped by year and inspection types for the pie chart
        $pieChartData = DB::table('visits')
            ->join('visit_site_mappings', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->join('inspection_types', 'visit_site_mappings.inspection_category_id', '=', 'inspection_types.id')
            ->selectRaw("
                YEAR(visits.arrival_datetime) as year,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 1 THEN 1 ELSE 0 END), 0) as schedule_1,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 2 THEN 1 ELSE 0 END), 0) as schedule_2,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 3 THEN 1 ELSE 0 END), 0) as schedule_3,
                COALESCE(SUM(CASE WHEN visit_site_mappings.inspection_category_id = 4 THEN 1 ELSE 0 END), 0) as schedule_4
            ")
            ->groupBy(DB::raw("YEAR(visits.arrival_datetime)"))
            ->whereNotNull('visits.arrival_datetime')
            ->whereNull('visits.deleted_at')
            ->whereRaw('YEAR(visits.arrival_datetime) >= ?', [$currentYear - 5])
            ->orderBy('year', 'ASC')
            ->get();


          $issueCounts = DB::table('visit_site_mappings')
            ->join('inspection_issues', 'inspection_issues.id', '=', 'visit_site_mappings.inspection_issue_id')
            ->join('visits', 'visits.id', '=', 'visit_site_mappings.visit_id')
            ->selectRaw('YEAR(visits.arrival_datetime) as year, inspection_issues.name as issue, COUNT(*) as total')
            ->whereNull('visits.deleted_at')
            ->whereIn('inspection_issues.name', ['GFI', 'RFA', 'Both', 'None', 'Others']) // Filter for the issue types
            ->groupBy(DB::raw('YEAR(visits.arrival_datetime), inspection_issues.name'))
            ->orderBy('year', 'ASC')
            ->get();

            $loginAndActivity = $this->getUserLastLoginAndActivity();
            
        return view('dashboard', [
            'inspectorCount' => $inspectorCount,
            'visitCount' => $visitCount,
            'opcwFaxeCount' => $opcwFaxeCount,
            'SequentialInspection' => $inspectionData->total ?? 0,
            'id' => $inspectionData->inspection_category_type_id ?? null,
            //'totalVisitsData' => $totalVisitsData,
            'currentYear' => $currentYear,
            'sequentialInspectionData' => $sequentialInspectionData,
            'years' => $years,
            'visits' => $visits,
            'pieChartData' => $pieChartData,
            'loginAndActivity' => $loginAndActivity,
            'nonSequentialInspectionData' => $nonSequentialInspectionData,
            'issueCounts' =>$issueCounts,
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



    private function logLoginAttempt(Request $request, string $status, string $details)
    {
        $user = Auth::user();
        $username = $user ? $user->name : 'Guest';
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




    public function getUserLastLoginAndActivity()
    {
        // Get the current logged-in user
        $user = auth()->user();

        // Retrieve the last login datetime from the login_logs table
        $lastLogin = DB::table('audit_trails')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')  // Order by the most recent login
            ->first();

        // Retrieve the last activity datetime from the activity_logs table
        $lastActivity = DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')  // Order by the most recent activity
            ->first();

        return compact('lastLogin', 'lastActivity');
    }


}




