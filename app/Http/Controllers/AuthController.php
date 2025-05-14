<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    protected $auditService;
    public function __construct(AuditService $auditService) {
        $this->auditService = $auditService;
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->auditService->log('login', "User logged in : {$user->email}", null, $user->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Login Success',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' =>'required|string|max:255',
            'email' =>'required|string|email|max:255|unique:users',
            'password' =>'required|string|min:6',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'voter'
        ]);

        $this->auditService->log('register', "User registered: {$user->email}", null, $user->toArray());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User Created Successfully',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);    
    }

    public function logout(Request $request) {
        $user = $request->user();

        $user->tokens()->delete();

        $this->auditService->log('logout', "User logged out: {$user->email}", null, $user->toArray());

        return response()->json([
           'success' => true,
           'message' => 'Logout Success',
        ], 200);
    }


    public function profile()
    {
        $user = Auth::user();
        return response()->json($user);
    }
}
