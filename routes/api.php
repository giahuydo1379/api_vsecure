<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('customer/register', 'APIRegisterController@register');
Route::post('customer/login', 'APILoginController@login');
Route::middleware('jwt.auth')->get('customers', function (Request $request) {
    return JWTAuth::toUser();
//    $user = JWTAuth::toUser($request->token);
//
//    return response()->json(compact('token', 'user'));
//    return auth()->user();
});
Route::get('devices', 'DeviceController@devices');
Route::get('device/customer/1', 'DeviceController@deviceUser');

Route::get('customers', 'CustomerController@customers');
Route::get('customers-by-email', 'CustomerController@customerByEmail');
Route::post('delete/device-token-by-email', 'CustomerController@deleteDevicetokenByEmail');
Route::get('customer/device/1', 'CustomerController@customersDevice');
Route::post('add/device-customer', 'CustomerController@insertDooAlarmCustomer');
Route::post('device-list/customer', 'CustomerController@deviceListCustomer');

Route::prefix('v1/customer')->group(function () {
    Route::get('/notify/show-all', 'NotifyController@showAll')->name('customer.show-all-notify');
});

