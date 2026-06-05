<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected function successResponse(
        $data = null,
        string $message = 'Success',
        int $statusCode = 200
    ){
        return response()->json([
            'status'=> true,
            'message'=> $message,
            'data'=>$data,
        ], $statusCode);
    }

    protected function errorResponse(
        string $message = 'Something went wrong',
        int $statusCode = 400,
        $errors = null
    ){
        return response()->json([
            'status'=> false,
            'message'=>$message,
            'errors'=> $errors,
        ],$statusCode);
    }
}
