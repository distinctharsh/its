<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class ProtectionController extends Controller
{
    public function toggle(Request $request)
    {
        $enabled = $request->input('enabled') ? '1' : '0';
        Setting::updateOrCreate(
            ['key' => 'protection_enabled'],
            ['value' => $enabled]
        );
        return response()->json(['success' => true]);
    }
} 