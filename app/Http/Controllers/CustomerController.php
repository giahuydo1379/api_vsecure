<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\DoorAlarmCustomer;
use App\Http\Models\Customer;
use DB;

class CustomerController extends Controller
{
    public function customers()
    {
        $data = [
            "status" => "OK",
            "data" => [
                "1" => [
                    "id" => 1,
                    "email" => "huy1@gmail.com",
                    "nick_name" => "huy",
                    "created_at" => "2019-01-04 09:35:44",
                    "updated_at" => "2019-01-04 09:35:44"
                ],
                "2" => [
                    "id" => 2,
                    "email" => "huy2@gmail.com",
                    "nick_name" => "huy2",
                    "created_at" => "2019-01-04 09:35:44",
                    "updated_at" => "2019-01-04 09:35:44"
                ],
            ]
        ];
        return response()->json($data, 200);
    }

    public function customersDevice()
    {
        $data = [
            "status" => "OK",
            "data" => [
                "1" => [
                    "id" => 1,
                    "email" => "huy1@gmail.com",
                    "nick_name" => "huy",
                    "created_at" => "2019-01-04 09:35:44",
                    "updated_at" => "2019-01-04 09:35:44",
                    "devices" => [
                        [
                            "id" => 1,
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
                        ],
                        [
                            "id" => 2,
                            "MAC" => "DC4f22BABA7B",
                            "name" => "huy2",
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
                        ]
                    ]


                ]

            ]
        ];
        return response()->json($data, 200);
    }

    public function insertDooAlarmCustomer(Request $request)
    {

        $result = array('status' => '');
        try {
            $flight = new DoorAlarmCustomer;
            $data = $request->all();

            $email = $request->email;

            $idCustomer = Customer::where('email', $data['email'])->pluck('id')->toArray();

            $data2 = [
                'mac_device' => $request->mac_device,
                'id_customer' => $idCustomer[0]
            ];

            $result['data'] = $flight::create($data2);


            $result['status'] = 200;
        } catch (QueryException $e) {
            $result['status'] = $e->getCode();
            $result['errMsg'] = $e->getMessage();
        }
        return $result;
    }

    public function deviceListCustomer(Request $request)
    {

        $result = array('status' => '');
        try {

            $data = $request->all();

            $email = $request->email;

            $idCustomer = Customer::where('email', $data['email'])->pluck('id')->toArray();


            // $mac_device = DB::table('dooralarm_customer')
            // -> select('mac_device')
            // ->where('id_customer', $idCustomer[0])
            // ->get();
            $mac_device = DoorAlarmCustomer::where('id_customer', $idCustomer[0])->pluck('mac_device')->toArray();
            // dd($mac_device);
            $result['data'] = DB::table('dooralarm')
                ->where('mac_device', $mac_device[0])
                ->get();
            $result['status'] = 200;
        } catch (QueryException $e) {
            $result['status'] = $e->getCode();
            $result['errMsg'] = $e->getMessage();
        }
        return $result;
    }

    public function customerByEmail(Request $request)
    {
        $email = $request->email;
        $customer = Customer::where("email", $email)->get();
//        dd($customer);
        $response = [
            'status' => '200',
            'data' => [
                'customer' => $customer,
            ]

        ];

        return response()->json($response);
    }

    public function deleteDevicetokenByEmail(Request $request)
    {
        $result = array('status' => '');
        $data = $request->all();
        $email = $request->email;
        $device_token = $request->device_token;
        $idCustomer = Customer::where('email', $email)->pluck('id')->toArray();
        $reponse = DB::table('device_token')
            ->where('customer_id', $idCustomer[0])
            ->where('device_token', $device_token)
            ->update([
                'is_deleted' => 1,
            ]);
        if ($reponse) {
            $result['status'] = 200;
            $result['message'] = 'Xóa thành công';
        }
        return $result;
    }

}
