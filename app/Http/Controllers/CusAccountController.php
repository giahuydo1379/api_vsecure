<?php
/**
 * Created by PhpStorm.
 * User: taishiro
 * Date: 1/17/19
 * Time: 9:33 AM
 */

namespace App\Http\Controllers;


use App\Http\Models\Customer;
use App\Http\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerLogin as RequestLogin;
use App\Http\Requests\CustomerLogout as RequestLogout;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CusAccountController extends Controller
{
    public function login(Request $request)
    {
        try {
            $requestCus = new RequestLogin();
            $validator = $requestCus->checkValidate($request);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $credentials = $request->only('email', 'password');
            $deviceTokenStr = $request->device_token;

            try {
                $token = JWTAuth::attempt($credentials);
                if (!$token)
                    return $this->responseFormat(404, trans('messages.not_found',['name'=> 'customer']));
                $customer = JWTAuth::toUser($token);
                $customerId = $customer->id;

                $existToken = $this->checkDeviceToken($customerId, $deviceTokenStr);
                if ($existToken)
                    return $this->responseFormat(422, trans('messages.not_logout'));
                $cusLogin = $this->checkDeviceTokenLogout($customerId);

                if (!$cusLogin) {
                    $create = $this->createDeviceToken($customer->id, $deviceTokenStr);
                    if ($create)
                        return $this->responseFormat(200, trans('messages.success'));
                    else
                        return $this->responseFormat(422, trans('messages.failed'));
                } else {
                    $update = $this->updateDeviceToken($cusLogin, $deviceTokenStr);
                    if ($update)
                        return $this->responseFormat(200, trans('messages.success'));
                    else
                        return $this->responseFormat(422, trans('messages.failed'));
                }
            } catch (JWTException $JWTException) {
                return $this->responseFormat(500, 'Service Error: No Auth ');
            }
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }

    }

    public function logout(Request $request)
    {
        try {
            $email = $request->email;
            $requestCus = new RequestLogout();
            $validator = $requestCus->checkValidate($request);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $customer = CustomerController::checkExistCustomer($email);
            if (!$customer)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'customer']));
            $cusLogin = $this->checkDeviceTokenLogout($customer->id);
            if ($cusLogin)
                return $this->responseFormat(422, trans('messages.not_login'));
            $deviceTokens = $customer->deviceToken;
            foreach ($deviceTokens as $deviceToken) {
                if ($deviceToken->dooralarm_id == null && $deviceToken->device_token == $request->device_token) {
                    $delDeviceToken = $this->deleteDeviceToken($deviceToken->id);
                    if (!$delDeviceToken)
                        return $this->responseFormat(402, trans('messages.logout_failed'));
                    return $this->responseFormat(200, trans('messages.logout_success'));
                }
            }
            return $this->responseFormat(404, trans('messages.not_found', ['name' => 'device token']));
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }

    /*
     * params int $customerId
     * params string $deviceToken
     * return boolean null | false | true => error | not_exist | exist
     * */

    private function checkDeviceToken($customerId, $deviceToken)
    {
        if (!$customerId)
            return true;
        else {
            $deviceToken = DeviceToken::where(['dooralarm_id' => null, 'customer_id' => $customerId,
                'device_token' => $deviceToken])->first();
            if ($deviceToken)
                return true;
            return false;
        }
    }

    private function checkDeviceTokenLogout($customerId)
    {
        if (!$customerId)
            return false;
        else {
            $deviceToken = DeviceToken::where(['dooralarm_id' => null, 'customer_id' => $customerId,
                'device_token' => ''])->first();
            if ($deviceToken)
                return $deviceToken->id;
            return false;
        }
    }

    private function createDeviceToken($customerId, $deviceToken)
    {
        if (!$customerId || !$deviceToken)
            return false;
        try {
            DeviceToken::create(['customer_id' => $customerId, 'device_token' => $deviceToken]);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function updateDeviceToken($deviceTokenId, $deviceToken)
    {

        if (!$deviceTokenId || !$deviceToken)
            return false;
        try {
            $device = DeviceToken::find($deviceTokenId);
            $device->device_token = $deviceToken;
            $device->save();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private function deleteDeviceToken($deviceTokenId)
    {
        try {
            if (!$deviceTokenId)
                return false;
            $deviceToken = DeviceToken::find($deviceTokenId);
            $deviceToken->update(['device_token' => '']);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

}