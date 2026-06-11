<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OtpRequested;
use App\Models\OtpVerification;

class AuthController extends Controller
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
}
