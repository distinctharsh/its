<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use App\Models\PermissionRole;
class PermissionController extends Controller
{
    //

    public function managePermission()
    {
        $permissions = Permission::all();
        return view('manage-permission', compact('permissions'));
    }

    public function createPermission(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'permission' => 'required|unique:permissions,name|max:255'
            ]);

            $permission = Permission::create([
                'name' => $validatedData['permission'],
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Permission Created!'
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

    public function assignPermissionRole(Request $request){
        $roles = Role::whereNotIn('name', ['Super Admin'])->get();
        $permissions = Permission::all();
        $permissionsWithRoles = Permission::with('roles')->whereHas('roles')->get();
        return view('assign-permission-role', compact('roles', 'permissions', 'permissionsWithRoles'));
    }

    public function createPermissionRole(Request $request){
        try {
            $isExistPermissionToRole = PermissionRole::where([
                'permission_id'=> $request->permission_id,
                'role_id'=> $request->role_id,
            ])->first();

            if($isExistPermissionToRole){
                return response()->json([
                    'success' => false,
                    'msg' => 'Permission is already assigned to selected role!'
                ]);
            }

            PermissionRole::create([
                'permission_id'=> $request->permission_id,
                'role_id'=> $request->role_id,
            ]);

            return response()->json([
                'success'=> true,
                'msg'=> 'Permission is assigned to selected role!'
            ]);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }
    }
}
