<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller{
    public function index(){
        return response()->json(Role::all()); // Get all roles
    }

    public function store(Request $request){
        $request->validate(['name' => 'required|string|unique:roles,name']);

        $role = Role::create(['name' => $request->name]);

        return response()->json($role, 201); // Create a new role
    }

    public function show($id){
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function update(Request $request, $id){
        $request->validate(['name' => 'required|string|unique:roles,name']);

        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);

        return response()->json($role);
    }

    public function destroy($id){
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}

