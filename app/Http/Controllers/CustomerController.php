<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\DoorAlarmCustomer;
use App\Http\Models\Customer;
use Illuminate\Database\QueryException;
use App\Http\Requests\Customer as CustomerRequest;
use DB;

class CustomerController extends Controller
{

    public function index()
    {
        $customer = Customer::all()->where('is_deleted', 0);
        if ($customer->isEmpty())
            return $this->responseFormat(404, 'Empty');
        return $this->responseFormat(200, 'Success', $customer);
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
        try {
            $email = $request->email;
            $customer = Customer::where("email", $email)->where('is_deleted', 0)->get();
//        dd($customer);
            $response = [
                'status' => '200',
                'data' => [
                    'customer' => $customer,
                ]

            ];
            return response()->json($response);
        } catch (QueryException $e) {
            $response['status'] = $e->getCode();
            $response['errMsg'] = $e->getMessage();
        }
        return $response;
    }

    public function deleteDevicetokenByEmail(Request $request)
    {
        try {
            $result = array('status' => '');
            $data = $request->all();
            $email = $request->email;
            $device_token = $request->device_token;
            $idCustomer = Customer::where('email', $email)->where('is_deleted', 0)->pluck('id')->toArray();
//        dd($data);
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

        } catch (QueryException $e) {
            $result['status'] = $e->getCode();
            $result['errMsg'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $email = $request->email;
        $requestCus = new CustomerRequest();
        $validator = $requestCus->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
        $customer = Customer::where(['email' => $email, 'is_deleted' => 0])->first();
        $info = $request->only('nick_name', 'password');
        if ($request->password)
            $info['password'] = bcrypt($info['password']);
        $customer->fill($info);
        if (!$customer->save())
            return $this->responseFormat(422, 'Failed');
        return $this->responseFormat(200, 'Success', $customer);
    }

    public function show(Request $request)
    {
        $email = $request->email;
        $requestCus = new CustomerRequest();
        $validator = $requestCus->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
        $customer = Customer::all()->where('email', $email);
        return $this->responseFormat(200, 'Success', $customer);
    }

}
