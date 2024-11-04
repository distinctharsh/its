<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    //

    public function manageRole()
    {
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        return view('manage-role', compact('roles'));
    }

    public function createRole(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'role' => 'required|unique:roles,name|max:255'
            ]);

            $role = Role::create([
                'name' => $validatedData['role'],
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Role Created!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'msg' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // General errors
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRole(Request $request)
    {
        try {
            Role::where('id', $request->role_id)->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Role Deleted!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'msg' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            // General errors
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ], 500);
        }
    }
}
