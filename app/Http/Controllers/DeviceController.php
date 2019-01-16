<?php

namespace App\Http\Controllers;

use App\Http\Models\Customer;
use App\Http\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Http\Requests\Customer as CustomerRequest;


class DeviceController extends Controller
{
    public function devices()
    {
        $data = [
            "status" => "OK",
            "data" => [
                "1" => [
                    "MAC" => "DC4f22BABA7B",
                    "name" => "huy1",
                    "password" => "123123",
                    "location" => "123123sdas",
                    "version" => "1.0",
                    "volume" => "2",
                    "arm_delay" => "5s",
                    "alarm_delay" => "5s",
                    "alarm_duration" => "5s",
                    "self_test_mode" => "normal",
                    "battery_capacity_reamaining" => "50%",
                ],
                "2" => [
                    "MAC" => "DC4f22BABA7B",
                    "name" => "huy1",
                    "password" => "123123",
                    "location" => "123123sdas",
                    "version" => "1.0",
                    "volume" => "2",
                    "arm_delay" => "5s",
                    "alarm_delay" => "5s",
                    "alarm_duration" => "5s",
                    "self_test_mode" => "normal",
                    "battery_capacity_reamaining" => "50%",
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function deviceUser()
    {
        $data = [
            "status" => "OK",
            "data" => [
                "1" => [
                    "MAC" => "DC4f22BABA7B",
                    "name" => "huy1",
                    "password" => "123123",
                    "location" => "123123sdas",
                    "version" => "1.0",
                    "volume" => "2",
                    "arm_delay" => "5s",
                    "alarm_delay" => "5s",
                    "alarm_duration" => "5s",
                    "self_test_mode" => "normal",
                    "is_arm" => 1,
                    'is_home' => 1,
                    'is_alarm' => 0,
                    'door_status' => 1,
                    "battery_capacity_reamaining" => "50%",
                    "customers" =>
                        [
                            [
                                "id" => 1,
                                "email" => "huy1@gmail.com",
                                "nick_name" => "huy",
                                "created_at" => "2019-01-04 09:35:44",
                                "updated_at" => "2019-01-04 09:35:44"
                            ],
                            [
                                "id" => 2,
                                "email" => "huy2@gmail.com",
                                "nick_name" => "huy2",
                                "created_at" => "2019-01-04 09:35:44",
                                "updated_at" => "2019-01-04 09:35:44"
                            ]

                        ]

                ]

            ]
        ];
        return response()->json($data, 200);
    }

    public function delete(Request $request)
    {
        try {
            $email = $request->email;
            $requestCus = new CustomerRequest();
            $validator = $requestCus->checkValidate($request, false);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $customer = Customer::where(['email' => $email, 'is_deleted' => 0])->first();
            if (!$customer)
                return $this->responseFormat(404, trans('messages.not_found', ['name' => 'customer']));
            $deviceTokens = $customer->deviceToken;
            $doorAlarms = $customer->doorAlarms;
            foreach ($doorAlarms as $doorAlarm) {
                if ($doorAlarm->mac == $request->mac){
                    $idDoorAlarm = $doorAlarm->id;
                    $deviceToken = DeviceToken::where();
                }
            }
            return $this->responseFormat(200, 'Success', $doorAlarms);
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }

    }
}
