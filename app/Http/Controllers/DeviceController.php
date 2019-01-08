<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


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
}