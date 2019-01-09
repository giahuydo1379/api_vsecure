<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
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
        $idCustomer = Customer::where('email', $data['email'])->pluck('id')->toArray();
        
         $insert = DB::table('device_token')->insert([
                  'customer_id' => $idCustomer[0],
                  'device_token' => $data['device_token'],
                  'created_at' =>  date('Y-m-d H:i:s'), 
                  'updated_at' =>  date('Y-m-d H:i:s'),
               ]);
        $response = [
            'status' => '200',
            'data' => [
                   'message' => 'login successful',
                   'token' => $token
            ]
         
        ];
        
        return response()->json($response);
    }
}
