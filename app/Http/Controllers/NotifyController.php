<?php

namespace App\Http\Controllers;

use App\Http\Models\Customer;
use App\Http\Requests\Notify;
use App\Http\Models\Notification as Notifications;
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
        $notifications = Notifications::all();
        $notify = array();
        foreach ($notifications as $notification) {
            $deviceToken = $notification->deviceToken;
//            dump($request->device_token.'----'.$deviceToken->device_token);
            if ($deviceToken->device_token == $request->device_token) {
                $notify['notify'][] = $notification;
                $customer_id = $deviceToken->customer_id;
                $customer = Customer::find($customer_id);
//                return $this->responseFormat(200,'ss',$deviceToken);
                $doorAlarms = $customer->doorAlarms;
                foreach ($doorAlarms as $doorAlarm) {
                    $notify['device'][] = $doorAlarm;
                }
            }
        }
        if (!$notify)
            return $this->responseFormat(404, trans('messages.not_found', ['name' => 'notification']));
        return $this->responseFormat(200, trans('messages.success'), $notify);
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
            $model = new DoorAlarm();
            $data = $request->all();
            $macAddress = $data['mac'];
            $doorAlarm = $model->where(['mac' => $macAddress])->first();
            unset($data['mac']);
            $doorAlarm->update($data);
            if ($doorAlarm->door_status == 1 && $doorAlarm->is_alarm == 0) {
                $this->connectRabbitmq('is_arm', $data['is_arm'], $macAddress);
            }
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
