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
        // dd($request->all());

        $request->validate([
            'email'=>'required|email'
        ]);

        $otp = random_int(100000, 999999);

        OtpVerification::updateOrCreate([
            'email'=>$request->email
        ],[
            'otp'=>$otp,
            'expires_at'=> now()->addMinutes(10)
        ]);

        event(new OtpRequested(
            $request->email,
            $otp
        ));

        return response()->json([
            'message' => 'email sent'
        ]);

    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email:rfc,dns',
            'otp'=>'required|digits:6'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $otp = OtpVerification::where('email',$request->email)
        ->where('otp',$request->otp)
        ->where('expires_at','>', Carbon::now())
        ->first();

        if(!$otp){

            return $this->errorResponse('Otp not found',404);

        }

        $token = Str::random(60);

        Cache::put(
            'register_'.$token,
            ['email'=> $request->email],
            now()->addMinutes(10)
        );

        return $this->successResponse(['token'=>$token],'Token');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
            'token' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(),422);
        }

        $data = Cache::get('register_'.$request->token);

         if(!$data){
            return response()->json([
                'message' => 'Token expired'
            ],400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $data['email'],
            'password' => Hash::make($request->password)
        ]);

        Cache::forget('register_'.$request->token);

         // Generate login token
        $accessToken = $user->createToken('auth-token')->plainTextToken;


        return $this->successResponse($user,'Registration successful',201);

        // return response()->json([
        //     'message' => 'Registration successful',
        //     'user' => $user,
        //     'token' => $accessToken
        // ],201);
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
