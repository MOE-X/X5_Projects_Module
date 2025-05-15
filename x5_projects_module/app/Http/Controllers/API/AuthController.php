<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
     * Register a new student.
     * The new user is automatically assigned the "student" role.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|string|min:6|max:32',
            'password_confirmation' => 'required|same:password',
            'phone'     => 'required|string|max:20',
            'dob'       => 'required|date',
            'gender_id' => 'required|exists:genders,id'
        ]);

        // Retrieve the "student" role from user_roles table.
        $studentRole = UserRole::where('name', 'student')->first();
        if (!$studentRole) {
            return response()->json(['message' => 'Student role is not defined.'], 500);
        }
        $validatedData['user_role_id'] = $studentRole->id;

        $validatedData['password'] = bcrypt($validatedData['password']);

        $user = new User($validatedData);
        $user->save();

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'data'    => $user,
            'token'   => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6|max:32',
        ]);

        $user = User::where('email', $request->email)->first();

        if(Auth::attempt($request->only('email', 'password'))){

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged in',
                'user' => Auth::user(),
                'token' => $user->createToken('authToken')->plainTextToken
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials!',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ], 200);
    } 
     
}
