<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OtpRequested;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Http\Controllers\Api\BaseController;
use Str;
use Cache;
use Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        $otp = random_int(100000, 999999);

        OtpVerification::updateOrCreate(
            ['email' => $request->email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10)
            ]
        );

        event(new OtpRequested(
            $request->email,
            $otp
        ));

        return response()->json([
            'message' => 'OTP sent successfully',
            'type' => $user ? 'login' : 'register'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
            'otp' => 'required|digits:6'
        ]);

        $otpVerification = OtpVerification::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpVerification) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 404);
        }

        $user = User::where('email', $request->email)->first();

        // Existing user
        if ($user) {

            return response()->json([
                'message' => 'OTP verified',
                'type' => 'login'
            ]);
        }

        // New user
        $token = Str::random(60);

        Cache::put(
            'register_' . $token,
            [
                'email' => $request->email
            ],
            now()->addMinutes(10)
        );

        return response()->json([
            'message' => 'OTP verified',
            'type' => 'register',
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $validator->errors(),
                422
            );
        }

        $data = Cache::get('register_' . $request->token);

        if (!$data) {
            return response()->json([
                'message' => 'Registration token expired'
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $data['email'],
            'password' => Hash::make($request->password)
        ]);

        Cache::forget('register_' . $request->token);

        $accessToken = $user->createToken('auth-token')
            ->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $accessToken
        ], 201);
    }

    public function profile(Request $request)
    {
        return $this->successResponse($request->user(),'User Details',200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email'=>'required',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return $this->errorResponse('Validation failed',422,$validator->errors());
        }

        if(!Auth::attempt($request->only('email','password')) ){
            return $this->errorResponse('Invalid credentials',401);
        }
        
        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            // 'user'=> $user,
            'token' =>$token
        ],'Login successful',200);
    }
}
