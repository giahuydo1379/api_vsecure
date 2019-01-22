<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use GuzzleHttp\Client as GuzzleClient;
use App\Http\Models\DoorAlarm;
use App\Http\Models\DeviceToken;
use DB;
use DateTime;


class PushNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushNotify:receive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//
        $RMQHOST = '118.69.80.100';
        $RMQPORT = 5672;
        $RMQUSER = 'ftpuser';
        $RMQPASS = 'FtpFdrive@#123$';

        $connection = new AMQPStreamConnection($RMQHOST, $RMQPORT, $RMQUSER, $RMQPASS);
        $channel = $connection->channel();

        $channel->queue_declare('door_alarm', false, false, false, false);


//        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
//        $channel = $connection->channel();

//        $channel->queue_declare('hello', false, false, false, false);


        $callback = function ($msg) {
            $this->processCallback($msg);
        };

        $channel->basic_consume('door_alarm', '', false, true, false, false, $callback);
//        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        echo " [x] Waiting...\n";
        while (count($channel->callbacks)) {
            $channel->wait();
        }
    }

    function processCallback($msg) {
        $message = json_decode($msg->body, true);
        $doorAlarm = DoorAlarm::where('mac', $message['mac_address'])->first();
        $query = DeviceToken::where(['dooralarm_id' => $doorAlarm->id, 'is_deleted' => 0]);
//        dd($doorAlarm->is_arm);
        $data['mac'] = $message['mac_address'];
        $data['is_home'] = isset($message['home_away']) ? $message['home_away'] : $doorAlarm->is_home;
        $data['is_alarm'] = isset($message['alarm_doorbell']) ? $message['alarm_doorbell'] : $doorAlarm->is_alarm;
        $data['battery_capacity_reamaining'] = isset($message['battery']) ? $message['battery'] : $doorAlarm->battery_capacity_reamaining;
        $data['is_arm'] = isset($message['disarming_arming']) ? $message['disarming_arming'] : $doorAlarm->is_arm;
        $data['door_status'] = isset($message['door_status']) ? $message['door_status'] : $doorAlarm->door_status;
//        dump($data);

//        dump($doorAlarm->toArray());


        $parentId = $query->pluck('parent_id')->toArray();
        $deviceTokens = array();
        $deviceTokenIds = array();
        foreach ($parentId as $id) {
            $query = DeviceToken::where(['id' => $id, 'is_deleted' => 0]);
            $deviceTokens[] = $query->pluck('device_token')->first();
            $deviceTokenIds[] = $query->pluck('id')->first();
        }

        dump($deviceTokens);
        dump($deviceTokenIds);

        $this->processNotifications($doorAlarm, $data, $deviceTokens, $deviceTokenIds);

        echo " [x] Done\n";
    }

    function updateDoorAlarm($data) {
        $macAddress = $data['mac'];
        unset($data['mac']);
        return DB::table('dooralarm')
            ->where('mac', $macAddress)
            ->update($data);
    }

    function pushNotify($deviceTokens, $data, $notify) {
        $client = new GuzzleClient([
            'base_uri' => 'https://fcm.googleapis.com/fcm/send',
        ]);

        if (!empty($deviceTokens)) {
            foreach ($deviceTokens as $deviceToken) {
                $client->post('', [
                    'headers' => [
                        'content-type' => 'application/json',
                        'Authorization ' => 'key=AIzaSyAbztHNWF15A3PSZ4Z1Won4YHjtRjOA9_M'
                    ],
                    'body' => json_encode([
                        'to' => $deviceToken,
                        "collapse_key" => "type_a",
                        "notification" => $notify,
                        "data" => [
                            'address_mac' => $data['mac'],
                            'home_away' => $data['is_home'],
                            'alarm_door_bell' => $data['is_alarm'],
                            'battery' => $data['battery_capacity_reamaining'],
                            'arming_dis_arming' => $data['is_arm'],
                            'door_status' => $data['door_status']
                        ]
                    ])

                ]);
//                    $body = $response->getBody();
//                    print_r(json_decode((string)$body));
            }
        } else {
            echo " [x] Not found device_token\n";
        }
    }

    function createLog($deviceTokenIds, $notify) {
        foreach ($deviceTokenIds as $deviceTokenId) {
            DB::table('notifications')->insert([
                'device_token_id' => $deviceTokenId,
                'action' => $notify['title'],
                'model_device' => "china",
                'push_time' => date('Y-m-d H:i:s')
            ]);
        };
    }

    function processNotifications($doorAlarm, $data, $deviceTokens, $deviceTokenIds) {

        $notify = [
            "body" => "Xin chào",
            "title" => "Xin chào",
        ];

        if ($doorAlarm->is_home != $data['is_home']) {
            if ($data['is_home'] == 0) {
                $notify = [
                    "body" => $doorAlarm->name . "Chuyển trạng thái ở nhà",
                    "title" =>  $doorAlarm->name ."Chuyển trạng thái ở nhà",
                ];
            } else {
                $notify = [
                    "body" =>  $doorAlarm->name . "Chuyển trạng thái đi vắng",
                    "title" =>  $doorAlarm->name ."Chuyển trạng thái đi vắng",
                ];
            }
            $this->pushNotify($deviceTokens, $data, $notify);
            $this->createLog($deviceTokenIds, $notify);

        }

        if ($doorAlarm->is_alarm != $data['is_alarm']) {
            if ($data['is_alarm'] == 0) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Chuyển trạng thái cảnh báo",
                    "title" =>  $doorAlarm->name ."Chuyển trạng thái cảnh báo",
                ];
            } else {
                $notify = [
                    "body" =>  $doorAlarm->name ."Chuyển trạng thái chuông cửa",
                    "title" =>  $doorAlarm->name ."Chuyển trạng thái chuông cửa",
                ];
            }
            $this->pushNotify($deviceTokens, $data, $notify);
            $this->createLog($deviceTokenIds, $notify);

        }

        if ($doorAlarm->battery_capacity_reamaining != $data['battery_capacity_reamaining']) {

            if ($data['battery_capacity_reamaining'] == 0) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Pin còn dưới 25%",
                    "title" =>  $doorAlarm->name ."Pin còn dưới 25%",
                ];
            }

            if ($data['battery_capacity_reamaining'] == 1) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Pin còn dưới 50%",
                    "title" =>  $doorAlarm->name ."Pin còn dưới 50%",
                ];
            }

            if ($data['battery_capacity_reamaining'] == 2) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Pin còn dưới 75%",
                    "title" =>  $doorAlarm->name ."Pin còn dưới 75%",
                ];
            }

            if ($data['battery_capacity_reamaining'] == 3) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Pin còn dưới 100%",
                    "title" =>  $doorAlarm->name ."Pin còn dưới 100%",
                ];
            }

            $this->pushNotify($deviceTokens, $data, $notify);
            $this->createLog($deviceTokenIds, $notify);

        }

        if ($doorAlarm->is_arm != $data['is_arm']) {
            if ($data['is_arm'] == 0) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Chuyển trạng thái tắt cảnh báo",
                    "title" =>  $doorAlarm->name ."Chuyển trạng thái tắt cảnh báo",
                ];
            }

            if ($data['is_arm'] == 1) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Chuyển trạng thái bật cảnh báo",
                    "title" =>  $doorAlarm->name ."Chuyển trạng thái bật cảnh báo",
                ];
            }

            $this->pushNotify($deviceTokens, $data, $notify);
            $this->createLog($deviceTokenIds, $notify);

        }

        if ($doorAlarm->door_status != $data['door_status']) {
            if ($data['door_status'] == 0) {
                $notify = [
                    "body" =>  $doorAlarm->name ."Cửa đóng",
                    "title" =>  $doorAlarm->name ."Cửa đóng",
                ];
            } else {
                $notify = [
                    "body" =>  $doorAlarm->name ."Cửa mở",
                    "title" =>  $doorAlarm->name ."Cửa mở",
                ];
            }

            $this->pushNotify($deviceTokens, $data, $notify);
            $this->createLog($deviceTokenIds, $notify);
        }
        // cập nhật table door_alarm sau khi gửi notify
        $this->updateDoorAlarm($data);
    }

}
