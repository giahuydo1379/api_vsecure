<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use GuzzleHttp\Client as GuzzleClient;
use App\Http\Models\DoorAlarmCustomer;
use App\Http\Models\DoorAlarm;
use App\Http\Models\DeviceToken;
use DB;


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
//        $RMQHOST = '118.69.80.100';
//        $RMQPORT = 5672;
//        $RMQUSER = 'ftpuser';
//        $RMQPASS = 'FtpFdrive@#123$';
//
//        $connection = new AMQPStreamConnection($RMQHOST, $RMQPORT, $RMQUSER, $RMQPASS);
//        $channel = $connection->channel();
//
//        $channel->queue_declare('door_alarm', false, false, false, false);


        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        $callback = function ($msg) {
//            dump($msg->body);
            $a = (strval($msg->body));
            $argv = json_decode($a, true);
            $address_mac = $argv['address_mac'];
            $is_home = $argv['home_away'];
            $is_alarm = $argv['alarm_door_bell'];
            $battery_capacity_reamaining = $argv['battery'];
            $is_arm = $argv['arming_dis_arming'];
            $door_status = $argv['door_status'];
//            dd($address_mac);
            $doorAlarmIdCustomers = DoorAlarmCustomer::where(['mac_device' => $address_mac])
                ->pluck('id_customer')->toArray();
            foreach ($doorAlarmIdCustomers as $doorAlarmIdCustomer) {
                $device_token[] = DeviceToken::where(['customer_id' => $doorAlarmIdCustomer, 'is_deleted' => 0])
                    ->pluck('device_token')->toArray();
            }
            $deviceTokens = array_flatten($device_token);
            $doorAlarmMac = DoorAlarm::where('mac_device', $address_mac)->first();
            $notify = [
                "body" => "Xin chào",
                "title" => "Xin chào",
            ];
            if ($doorAlarmMac->is_home != $is_home) {
                if ($is_home == 0) {
                    $notify = [
                        "body" => "Chuyển trạng thái ở nhà",
                        "title" => "Chuyển trạng thái ở nhà",
                    ];
                } else {
                    $notify = [
                        "body" => "Chuyển trạng thái đi vắng",
                        "title" => "Chuyển trạng thái đi vắng",
                    ];
                }

            } elseif ($doorAlarmMac->is_alarm != $is_alarm) {
                if ($is_alarm == 0) {
                    $notify = [
                        "body" => "Chuyển trạng thái alarm",
                        "title" => "Chuyển trạng thái alarm",
                    ];
                } else {
                    $notify = [
                        "body" => "Chuyển trạng thái doorbell",
                        "title" => "Chuyển trạng thái doorbell",
                    ];
                }

            } elseif ($doorAlarmMac->battery_capacity_reamaining != $battery_capacity_reamaining) {
                if ($battery_capacity_reamaining == 0) {
                    $notify = [
                        "body" => "Pin còn dưới 25%",
                        "title" => "Pin còn dưới 25%",
                    ];
                }
                if ($battery_capacity_reamaining == 1) {
                    $notify = [
                        "body" => "Pin còn dưới 50%",
                        "title" => "Pin còn dưới 50%",
                    ];
                }
                if ($battery_capacity_reamaining == 2) {
                    $notify = [
                        "body" => "Pin còn dưới 75%",
                        "title" => "Pin còn dưới 75%",
                    ];
                } else {
                    $notify = [
                        "body" => "Pin còn dưới 100%",
                        "title" => "Pin còn dưới 100%",
                    ];
                }

            } elseif ($doorAlarmMac->is_arm != $is_arm) {
                if ($is_arm == 0) {
                    $notify = [
                        "body" => "Chuyển trạng thái disarm",
                        "title" => "Chuyển trạng thái disarm",
                    ];
                } else {
                    $notify = [
                        "body" => "Chuyển trạng thái arm",
                        "title" => "Chuyển trạng thái arm",
                    ];
                }


            } elseif ($doorAlarmMac->door_status != $door_status) {
                if ($door_status == 0) {
                    $notify = [
                        "body" => "Cửa đóng",
                        "title" => "Cửa đóng",
                    ];
                } else {
                    $notify = [
                        "body" => "Cửa đóng",
                        "title" => "Cửa đóng",
                    ];
                }
            }

            $doorAlarm = DoorAlarm::updateOrCreate(
                ['mac_device' => $address_mac],
                ['is_home' => $is_home,
                    'is_alarm' => $is_alarm,
                    'battery_capacity_reamaining' => $battery_capacity_reamaining,
                    'is_arm' => $is_arm,
                    'door_status' => $door_status]
            );


            $client = new GuzzleClient([
                'base_uri' => 'https://fcm.googleapis.com/fcm/send',
            ]);
            if ($deviceTokens) {
                foreach ($deviceTokens as $deviceToken) {
                    $response = $client->post('', [
                        'headers' => [
                            'content-type' => 'application/json',
                            'Authorization ' => 'key=AIzaSyAbztHNWF15A3PSZ4Z1Won4YHjtRjOA9_M'
                        ],
                        'body' => json_encode([
                            'to' => $deviceToken,
                            "collapse_key" => "type_a",
                            "notification" => $notify,
                            "data" => [
                                'address_mac' => $argv['address_mac'],
                                'home_away' => $is_home,
                                'alarm_door_bell' => $is_alarm,
                                'battery' => $battery_capacity_reamaining,
                                'arming_dis_arming' => $is_arm,
                                'door_status' => $door_status
                            ],
                        ])

                    ]);
                    $body = $response->getBody();
                    print_r(json_decode((string)$body));
                }
            } else {
                echo " [x] Not found device_token\n";
            }

            echo " [x] Done\n";
        };

//        $channel->basic_consume('door_alarm', '', false, true, false, false, $callback);
        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        echo " [x] Waiting...\n";
        while (count($channel->callbacks)) {
            $channel->wait();
        }

    }


}
