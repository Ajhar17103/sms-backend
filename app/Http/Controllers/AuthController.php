<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller{
    public function register(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|integer|exists:roles,id'
        ]);
    
        // Create user with role_id
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'role_id' => $validatedData['role'],
        ]);
    
        $token = Auth::guard('api')->login($user);
    
        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user,
            // 'token' => $token
        ], 201);
    }
    
    public function login(Request $request){
        $credentials = $request->only('email', 'password');
    
        // Attempt to log the user in
        if ($token = Auth::guard('api')->attempt($credentials)) {
            // Get the authenticated user
            $user = Auth::guard('api')->user();
    
            // Return the user details along with the role_id
            return response()->json([
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id, // Include role_id
                ]
            ]);
        }
    
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    

    public function userDetails(){
        return response()->json(Auth::guard('api')->user());
    }

    public function logout(){
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh(){
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    protected function respondWithToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ]);
    }
}
