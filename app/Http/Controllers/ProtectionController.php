<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Services\LoggingService;

class ProtectionController extends Controller
{
    public function toggle(Request $request)
    {
        $enabled = $request->input('enabled') ? '1' : '0';
        $setting = Setting::updateOrCreate(
            ['key' => 'protection_enabled'],
            ['value' => $enabled]
        );
        // Log activity
        LoggingService::logActivity(
            $request,
            'update',
            'settings',
            $setting->id,
            ['key' => 'protection_enabled', 'value' => $enabled]
        );
        return response()->json(['success' => true]);
    }
} 