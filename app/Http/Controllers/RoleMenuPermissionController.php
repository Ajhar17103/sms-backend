<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Menu;
use App\Models\RoleMenuPermission;
use Illuminate\Http\Request;

class RoleMenuPermissionController extends Controller{
    // Assign permissions to a role for a specific menu
    public function assignPermissions(Request $request){
        $request->validate([
            'menuId' => 'required|exists:menus,id',
            'roleId' => 'required|exists:roles,id',
            'view' => 'nullable|boolean',
            'add' => 'nullable|boolean',
            'edit' => 'nullable|boolean',
            'delete' => 'nullable|boolean',
            'assign' => 'nullable|boolean',
            'submit' => 'nullable|boolean',
        ]);

        // Retrieve the role and menu
        $role = Role::findOrFail($request->roleId);
        $menu = Menu::findOrFail($request->menuId);

        // Check if permissions already exist for the role and menu
        $permissions = RoleMenuPermission::where('role_id', $request->roleId)
                                         ->where('menu_id', $request->menuId)
                                         ->first();

        if ($permissions) {
            // Update existing permissions
            $permissions->update([
                'view' => $request->view ?? $permissions->view,
                'add' => $request->add ?? $permissions->add,
                'edit' => $request->edit ?? $permissions->edit,
                'delete' => $request->delete ?? $permissions->delete,
                'assign' => $request->assign ?? $permissions->assign,
                'submit' => $request->submit ?? $permissions->submit,
            ]);
        } else {
            // Create new permissions entry
            RoleMenuPermission::create([
                'role_id' => $request->roleId,
                'menu_id' => $request->menuId,
                'view' => $request->view ?? false,
                'add' => $request->add ?? false,
                'edit' => $request->edit ?? false,
                'delete' => $request->delete ?? false,
                'assign' => $request->assign ?? false,
                'submit' => $request->submit ?? false,
            ]);
        }

        return response()->json([
            'message' => 'Permissions assigned successfully',
            'role_id' => $request->roleId,
            'menu_id' => $request->menuId,
        ], 200);
    }

    public function updatePermissions(Request $request){
        // Validate the incoming request data
        $request->validate([
            'menuId' => 'required|exists:menus,id',
            'roleId' => 'required|exists:roles,id',
            'view'   => 'nullable|boolean',
            'add'    => 'nullable|boolean',
            'edit'   => 'nullable|boolean',
            'delete' => 'nullable|boolean',
            'assign' => 'nullable|boolean',
            'submit' => 'nullable|boolean',
        ]);
    
        // Find the existing permission record
        $permissions = RoleMenuPermission::where('role_id', $request->roleId)
                                         ->where('menu_id', $request->menuId)
                                         ->first();
    
        // Check if the permission record exists
        if (!$permissions) {
            return response()->json([
                'message' => 'Permission record not found. Assign permissions first.'
            ], 404);
        }
    
        // Update the permission record with the new values if provided
        $permissions->update([
            'view'   => $request->has('view') ? $request->view : $permissions->view,
            'add'    => $request->has('add') ? $request->add : $permissions->add,
            'edit'   => $request->has('edit') ? $request->edit : $permissions->edit,
            'delete' => $request->has('delete') ? $request->delete : $permissions->delete,
            'assign' => $request->has('assign') ? $request->assign : $permissions->assign,
            'submit' => $request->has('submit') ? $request->submit : $permissions->submit,
        ]);
    
        // Return a success response
        return response()->json([
            'message' => 'Permissions updated successfully',
            'role_id' => $request->roleId,
            'menu_id' => $request->menuId,
        ], 200);
    }
    public function getRoleMenuPermissions($roleId){
    // Validate role existence
    $role = Role::find($roleId);
    if (!$role) {
        return response()->json(['message' => 'Role not found'], 404);
    }

    // Fetch all menus assigned to the role along with permissions
    $permissions = RoleMenuPermission::where('role_id', $roleId)->get()->keyBy('menu_id');

    // Fetch all main menus (parent_id = null)
    $menus = Menu::whereNull('parent_id')->get();

    // Recursive function to build menu tree with permissions
    $buildMenuTree = function ($menus, $permissions) use (&$buildMenuTree) {
        return $menus->map(function ($menu) use ($permissions, $buildMenuTree) {
            $menuPermissions = $permissions[$menu->id] ?? null;

            // Define permission values
            $permissionsArray = [
                'view'   => $menuPermissions ? (bool) $menuPermissions->view : false,
                'add'    => $menuPermissions ? (bool) $menuPermissions->add : false,
                'edit'   => $menuPermissions ? (bool) $menuPermissions->edit : false,
                'delete' => $menuPermissions ? (bool) $menuPermissions->delete : false,
                'assign' => $menuPermissions ? (bool) $menuPermissions->assign : false,
                'submit' => $menuPermissions ? (bool) $menuPermissions->submit : false,
            ];

            // Determine if any permission is assigned
            $permissionsArray['allowed'] = array_reduce(
                $permissionsArray,
                fn ($carry, $value) => $carry || $value,
                false
            );

            // Format menu with permissions
            $menuData = array_merge([
                'menuId'    => $menu->id,
                'name'      => $menu->name,
                'type'      => $menu->type,
                'parent_id' => $menu->parent_id,
                'roleId'    => $menuPermissions ? $menuPermissions->role_id : null,
            ], $permissionsArray);

            // Fetch sub-menus recursively
            $subMenus = Menu::where('parent_id', $menu->id)->get();
            $menuData['sub_menus'] = $subMenus->isNotEmpty() ? $buildMenuTree($subMenus, $permissions) : [];

            return $menuData;
        });
    };

    // Build menu tree with permissions
    $menuTree = $buildMenuTree($menus, $permissions);

    return response()->json($menuTree, 200);
}

    public function getAllRoleMenuPermissions(){
    // Fetch all roles
    $roles = Role::all();

    // Fetch all role-based permissions
    $permissions = RoleMenuPermission::all()->groupBy('role_id');

    // Fetch all main menus
    $menus = Menu::whereNull('parent_id')->get();

    // Recursive function to build menu tree with permissions
    function buildMenuTree($menus, $permissions, $roleId)
    {
        return $menus->map(function ($menu) use ($permissions, $roleId) {
            $menuPermissions = $permissions->get($roleId, collect())->where('menu_id', $menu->id)->first();

            // Format menu with permissions
            $menuData = [
                'menuId' => $menu->id,
                'name' => $menu->name,
                'type' => $menu->type,
                'parent_id' => $menu->parent_id,
                'roleId' => $roleId,
                'view' => $menuPermissions ? (bool) $menuPermissions->view : false,
                'add' => $menuPermissions ? (bool) $menuPermissions->add : false,
                'edit' => $menuPermissions ? (bool) $menuPermissions->edit : false,
                'delete' => $menuPermissions ? (bool) $menuPermissions->delete : false,
                'assign' => $menuPermissions ? (bool) $menuPermissions->assign : false,
                'submit' => $menuPermissions ? (bool) $menuPermissions->submit : false,
            ];

            // Fetch sub-menus recursively
            $subMenus = Menu::where('parent_id', $menu->id)->get();
            if ($subMenus->isNotEmpty()) {
                $menuData['sub_menus'] = buildMenuTree($subMenus, $permissions, $roleId);
            } else {
                $menuData['sub_menus'] = [];
            }

            return $menuData;
        });
    }

    // Build role-based menu permission structure
    $roleMenuPermissions = $roles->map(function ($role) use ($menus, $permissions) {
        return [
            'roleId' => $role->id,
            'roleName' => $role->name,
            'menus' => buildMenuTree($menus, $permissions, $role->id),
        ];
    });

    return response()->json($roleMenuPermissions, 200);
    }


}
