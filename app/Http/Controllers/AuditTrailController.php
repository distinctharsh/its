<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function loginLogs(){
        $auditTrails = AuditTrail::all();
        return view('audit-login-logs', compact('auditTrails'));
    }

    public function activityLogs(){
        $activityLogs = ActivityLog::all();
        return view('audit-activity-logs', compact('activityLogs'));
    }
}
