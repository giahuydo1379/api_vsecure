<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use JWTAuth;
use Validator;
use Response;

class APIRegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:customers',
            'nick_name' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        Customer::create([
            'nick_name' => $request->get('nick_name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);
        $customer = Customer::first();

        $token = JWTAuth::fromUser($customer);

        return Response::json(compact('token'));
    }
}
