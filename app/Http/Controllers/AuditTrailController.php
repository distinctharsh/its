<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function loginLogs(Request $request)
    {
        $query = AuditTrail::query();

        // Apply from date filter if provided
        if ($request->has('startDate') && !empty($request->startDate)) {
            $query->whereDate('created_at', '>=', $request->startDate);
        }
        if ($request->has('endDate') && !empty($request->endDate)) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        if ($request->has('action_detail') && $request->action_detail != 'all') {
            $query->where('action_details', $request->action_detail);
        }

        // Order by 'created_at' in descending order to show the latest logs first
        $auditTrails = $query->orderBy('created_at', 'desc')->get();

        // Return the view with the filtered data and filters
        return view('audit-login-logs', compact('auditTrails', 'request'));
    }


    public function activityLogs(Request $request)
    {
        // $query = ActivityLog::query();

        $query = ActivityLog::with('user');
        // Apply action type filter if provided
        if ($request->has('action_type') && $request->action_type != 'all') {
            $query->where('action_type', $request->action_type);
        }

        // Apply start date filter if provided
        if ($request->has('startDate') && !empty($request->startDate)) {
            $query->whereDate('created_at', '>=', $request->startDate);
        }

        // Apply end date filter if provided
        if ($request->has('endDate') && !empty($request->endDate)) {
            $query->whereDate('created_at', '<=', $request->endDate);
        }

        // Get the filtered results
        // Order the results by created_at in descending order
        $activityLogs = $query->orderBy('created_at', 'desc')->get();

        return view('audit-activity-logs', compact('activityLogs'));
    }
}
