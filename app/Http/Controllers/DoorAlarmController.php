<?php

namespace App\Http\Controllers;

use App\Http\Models\Customer;
use App\Http\Models\DeviceToken;
use App\Http\Requests\Customer as CustomerRequest;
use App\Http\Models\DoorAlarm;
use Illuminate\Http\Request;

class DoorAlarmController extends Controller
{
    private $doorAlarm;

    public function __construct()
    {
        $this->doorAlarm = new DoorAlarm();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $email = $request->email;
        $requestCus = new CustomerRequest();
        $validator = $requestCus->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
//        $cusId = Customer::where(['email' => $email, 'is_deleted' => 0])->pluck('id')->first();
//        if (!$cusId)
//            return $this->responseFormat(404, 'Not found customer');
//        $doorAlarmId = DeviceToken::where(['customer_id' => $cusId])->pluck('dooralarm_id');
//        if (!$doorAlarmId)
//            return $this->responseFormat(404, 'Not found door alarm');
//        $doorAlarm = DoorAlarm::find($doorAlarmId);
//        if (!$doorAlarm)
//            return $this->responseFormat(404, 'Empty');
        $doorAlarmData = [
            "id" => 1,
            "email" => "huy1@gmail.com",
            "nick_name" => "huy",
            "created_at" => "2019-01-04 09:35:44",
            "updated_at" => "2019-01-04 09:35:44",
            "devices" => [
                [
                    "id" => 1,
                    "MAC" => "DC:4f:22:BA:BA:7B",
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
                    "MAC" => "DC:4f:22:BA:BA:7B",
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
        ];
        return $this->responseFormat(200, 'Success', $doorAlarmData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestDoorAlarm = new DoorAlarmRequest();
        $validator = $requestDoorAlarm->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
//        $cusId = Customer::where(['email'=>$request->email,'is_deleted'=> 0])->pluck('id')->first();
//        if (!$cusId)
//            return $this->responseFormat(404, 'Not found');
        $doorAlarm = new DoorAlarm();
        $doorAlarm->fill($request->all());

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $doorAlarm = DoorAlarm::find($id);
            $this->doorAlarm = new DoorAlarm([
                "Id" => '1',
                "mac" => "DC4f22BABA7B",
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
            ]);
//            if (!$doorAlarm)
//                return $this->responseFormat(404, 'Not found');
//            if (!$doorAlarm->delete())
//                return $this->responseFormat(422, 'Deleted failed');
            return $this->responseFormat(200, 'Success');

        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }

    public function delete(Request $request)
    {
        try {
            $email = $request->email;
            $deviceToken = $request->device_token;
            $requestDoorAlarm = new DoorAlarmRequest();
            $validator = $requestDoorAlarm->checkValidate($request, false);
            if ($validator->fails())
                return $this->responseFormat(422, $validator->errors());
            $cusId = Customer::where(['email' => $email, 'is_deleted' => 0])->pluck('id')->first();
            $cusId = 1; // test
            if (!$cusId)
                return $this->responseFormat(404, 'Not found customer');
            $doorAlarmId = DeviceToken::where(['customer_id' => $cusId, 'device_token' => $deviceToken])
                ->pluck('id')->first();
            $doorAlarmId = 1; //test
            if (!$doorAlarmId)
                return $this->responseFormat(404, 'Not found door alarm');
            return $this->destroy($doorAlarmId);
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }
    }
}
