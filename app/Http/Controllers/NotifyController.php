<?php

namespace App\Http\Controllers;

use App\Http\Models\Customer;
use App\Http\Requests\Notify;
use App\Http\Models\Notify as Notifications;
use Illuminate\Http\Request;
use App\Http\Models\DoorAlarm;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class NotifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $requestNotify = new Notify();
        $validator = $requestNotify->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
//        $customer = CustomerController::checkExistCustomer($request->email);
//        if (!$customer)
//            return $this->responseFormat(404, trans('messages.not_found', ['name' => 'customer']));
        $notifications = Notifications::all();
        $notify = array();
        foreach ($notifications as $notification) {
            $notify['notify'][] = $notification;
            $deviceToken = $notification->deviceToken;
            if ($deviceToken->device_token == $request->device_token) {

                $customer_id = $deviceToken->customer_id;
                $customer = Customer::find($customer_id);
//                return $this->responseFormat(200,'ss',$deviceToken);
                $doorAlarms = $customer->doorAlarms;
                foreach ($doorAlarms as $doorAlarm) {
                   $notify['device'][] = $doorAlarm;
                }
                return $this->responseFormat(200, trans('messages.success'), $notify);
            }
        }
        return $this->responseFormat(404, trans('messages.not_found',['name'=> 'notify']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    public function showAll(Request $request)
    {
        $requestNotify = new Notify();
        $validator = $requestNotify->checkValidate($request);
        if ($validator->fails())
            return $this->responseFormat(422, $validator->errors());
        $data = [
            [
                "id" => 1,
                "dooralarm_name" => "huy", // string
                "action" => "action1",  //string
                "time_push" => "2019-01-04 09:35:44", // current time  created_at
                "mac_address" => " device1", //
            ],
            [
                "id" => 2,
                "nick_name" => "huy",
                "action" => "action2",
                "time_push" => "2019-01-04 09:35:44",
                "model_device" => " device2",
            ],
        ];
        return $this->responseFormat(200, 'Success', $data);
    }

    public function receiveReponseFromApp(Request $request)
    {
        try {
            $mac = $request->mac;
            $is_arm = $request->is_arm;
            $volume = $request->volume;
            $arm_delay = $request->arm_delay;
            $alarm_delay = $request->alarm_delay;
            $alarm_duration = $request->alarm_duration;
            $self_test_mode = $request->self_test_mode;
            $timing_arm_disarm = $request->timing_arm_disarm;
            $doorAlarmMac = DoorAlarm::where('mac', $mac)->first();
            if($doorAlarmMac->door_status == 1 && $doorAlarmMac-> is_alarm == 0){
                $this->connectRabbitmq('is_arm', $is_arm, $mac);
            }
            if ($doorAlarmMac->is_arm != $is_arm) {
                $this->updateReponseFromApp('is_arm', $is_arm, $mac);
            }
            if ($doorAlarmMac->volume != $volume) {
                $this->updateReponseFromApp('volume', $volume, $mac);
            }
            if ($doorAlarmMac->arm_delay != $arm_delay) {
                $this->updateReponseFromApp('arm_delay', $arm_delay, $mac);
            }
            if ($doorAlarmMac->alarm_delay != $alarm_delay) {
                $this->updateReponseFromApp('alarm_delay', $alarm_delay, $mac);
            }
            if ($doorAlarmMac->alarm_duration != $alarm_duration) {
                $this->updateReponseFromApp('alarm_duration', $alarm_duration, $mac);
            }
            if ($doorAlarmMac->self_test_mode != $self_test_mode) {
                $this->updateReponseFromApp('self_test_mode', $self_test_mode, $mac);
            }
            if ($doorAlarmMac->timing_arm_disarm != $timing_arm_disarm) {
                $this->updateReponseFromApp('timing_arm_disarm', $timing_arm_disarm, $mac);
            }
//            dd($doorAlarmMac);
            return $this->responseFormat(200, 'Success');
        } catch (\Exception $exception) {
            return $this->responseFormat(500, 'Service Error' . $exception->getMessage());
        }

    }

    public function updateReponseFromApp($type, $data, $mac)
    {
        $update = DoorAlarm::where('mac', $mac)->update([
            $type => $data,
        ]);
        return $this->responseFormat(200, 'Success');
    }

    public function connectRabbitmq($type, $data, $mac)
    {
        $RMQHOST = '118.69.80.100';
        $RMQPORT = 5672;
        $RMQUSER = 'ftpuser';
        $RMQPASS = 'FtpFdrive@#123$';

        $connection = new AMQPStreamConnection($RMQHOST, $RMQPORT, $RMQUSER, $RMQPASS);
        $channel = $connection->channel();

//        $channel->queue_declare('pushReponse', false, false, false, false);

//        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
//        $channel = $connection->channel();

//        $channel->queue_declare('hello2', false, false, false, false);
        $channel->exchange_declare('dis_arming', 'direct', false, false, false);
        $argv = [
            $type => $data,
        ];

        $argv = json_encode($argv);

        $msg = new AMQPMessage($argv);

//        $channel->basic_publish($msg, '', 'pushReponse');
//        $channel->basic_publish($msg, '', 'hello2');
        $channel->basic_publish($msg, 'direct_logs', $mac);

        echo ' [x] Sent ', "\n";
    }


}
