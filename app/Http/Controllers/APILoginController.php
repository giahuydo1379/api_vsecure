<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use DB;

class APILoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        $data = $request -> all();
     
        $credentials = $request->only('email', 'password');
//        dd($token = JWTAuth::attempt($credentials));
        try {

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
         $updateData = DB::table('customers')
                        ->where('email', $data['email'])
                        ->update([
                            'token_notification' => $data['token_notification'],
                        ]);
        $response = [
            'status' => '200',
            'message' => 'login successful',
            'token' => $token
        ];
        
        return response()->json($response);
    }
}
