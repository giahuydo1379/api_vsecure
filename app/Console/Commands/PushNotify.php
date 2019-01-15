<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use GuzzleHttp\Client as GuzzleClient;
use App\Http\Models\DoorAlarmCustomer;
use App\Http\Models\DeviceToken;

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

        $RMQHOST = '118.69.80.100';
        $RMQPORT = 5672;
        $RMQUSER = 'ftpuser';
        $RMQPASS = 'FtpFdrive@#123$';

        $connection = new AMQPStreamConnection($RMQHOST, $RMQPORT, $RMQUSER, $RMQPASS);
        $channel = $connection->channel();

        $channel->queue_declare('door_alarm', false, false, false, false);


        $callback = function ($msg) {
//            dump($msg->body);
            $a = (strval($msg->body));
            $argv = json_decode($a, true);
            $address_mac = $argv['address_mac'];
//            dd($address_mac);
            $doorAlarmIdCustomers = DoorAlarmCustomer::where(['mac_device' => $address_mac])
                ->pluck('id_customer')->toArray();
            foreach ($doorAlarmIdCustomers as $doorAlarmIdCustomer) {
                $device_token[] = DeviceToken::where(['customer_id' => $doorAlarmIdCustomer, 'is_deleted' => 0])
                    ->pluck('device_token')->toArray();
            }
            $deviceTokens = array_flatten($device_token);
//            dd($deviceTokens);

//            $client = new GuzzleClient([
//                'base_uri' => 'https://fcm.googleapis.com/fcm/send',
//            ]);
//            $response = $client->post('', [
//                'headers' => [
//                    'Content-Type' => 'application/json',
////                    'Content-Type' => 'application/x-www-form-urlencoded',
//                    'Authorization ' => 'key=AIzaSyAbztHNWF15A3PSZ4Z1Won4YHjtRjOA9_M'
//                ],
//                'body' => json_encode([
//                    'to' => 'dBbvVxK9k44:APA91bFBGkogWk7ObiO7ttRPUnG26OG_AJ9SgRjfgpRwGP0rEUaIt68C_usEYPpbspkPIVy1ptv5V00XBFTgZzQ6vV3XmgUeMkpUMHt0lkjE_QMWT7i3Gd9D8e0NuSX33nrJh-1pLTIC',
//                    "collapse_key" => "type_a",
//                    "notification" => [
//                        "body" => "cua mo",
//                        "title" => "Collapsing A",
//                    ],
//                    "data" => [
//                        "body" => "First Notification",
//                        "title" => "Collapsing A",
//                        "key_1" => "Data for key one",
//                        "key_2" => "Hellowww"
//                    ],
//                ])
//            ]);
//            $body = $response->getBody();
////            dd($body);
//            print_r(json_decode((string)$body));

//            dd($client);

            $client = new GuzzleClient([
                'base_uri' => 'https://fcm.googleapis.com/fcm/send',
            ]);
            if($deviceTokens){
                foreach ($deviceTokens as $deviceToken) {
                    $response = $client->post('', [
                        'headers' => [
                            'content-type' => 'application/json',
                            'Authorization ' => 'key=AIzaSyAbztHNWF15A3PSZ4Z1Won4YHjtRjOA9_M'
                        ],
                        'body' => json_encode([
                            'to' => $deviceToken,
                            "collapse_key" => "type_a",
                            "notification" => [
                                "body" => "cua mo",
                                "title" => "Collapsing A",
                            ],
                            "data" => [
                                "body" => "First Notification",
                                "title" => "Collapsing A",
                                "key_1" => "Data for key one",
                                "key_2" => "Hellowww"
                            ],
                        ])

                    ]);
                    $body = $response->getBody();
                    print_r(json_decode((string)$body));
                }
            }else{
                echo " [x] Not found device_token\n";
            }

            echo " [x] Done\n";
        };

        $channel->basic_consume('door_alarm', '', false, true, false, false, $callback);
        echo " [x] Waiting...\n";
        while (count($channel->callbacks)) {
            $channel->wait();
        }

    }


}
