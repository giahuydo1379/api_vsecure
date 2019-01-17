<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

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
App::setLocale('vi');
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
Route::post('reponse/app', 'NotifyController@receiveReponseFromApp');
Route::get('test', 'NotifyController@test');

Route::prefix('v1/customer')->group(function () {
    Route::get('/', 'CustomerController@index')->name('customer.list-all');
    Route::post('/login', 'CusAccountController@login')->name('customer.login');
    Route::post('/logout', 'CusAccountController@logout')->name('customer.logout');
    Route::get('show', 'CustomerController@show')->name('customer.details');
    Route::post('/update', 'CustomerController@update')->name('customer.update');
    Route::get('/notify/show-all', 'NotifyController@showAll')->name('customer.show-all-notify');

});
Route::prefix('v1/device-token')->group(function () {
    Route::post('delete', 'DeviceController@delete')->name('door-alarm.delete');
});
Route::prefix('v1/door-alarm')->group(function () {
    Route::post('insert', 'DoorAlarmController@insert')->name('door-alarm.insert');
    Route::get('/', 'DoorAlarmController@index')->name('door-alarm.show-all');
    Route::get('show', 'DoorAlarmController@show')->name('door-alarm.show-by-email');
    Route::get('/customer', 'DoorAlarmController@showCustomer')->name('door-alarm.show-all-customer');
    Route::post('create', 'DoorAlarmController@store')->name('door-alarm.store');
    Route::post('/edit', 'DoorAlarmController@update')->name('door-alarm.edit');
    Route::post('/share', 'DoorAlarmController@share')->name('door-alarm.share');
    Route::post('delete', 'DoorAlarmController@delete')->name('door-alarm.delete');

});

