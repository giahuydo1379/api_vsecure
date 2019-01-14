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
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);


        $callback = function ($msg) {
            $a = (strval($msg->body));
            $argv = json_decode ($a, true);
            $address_mac = $argv['address_mac'];
            $doorAlarmIdCustomers = DoorAlarmCustomer::where(['mac_device' => $address_mac])
                ->pluck('id_customer')->toArray();
//            dd($doorAlarmIdCustomer);
            foreach ($doorAlarmIdCustomers as $doorAlarmIdCustomer ){
                $device_token[] = DeviceToken::where(['customer_id' => $doorAlarmIdCustomer, 'is_deleted' => 0])
                    ->pluck('device_token')->toArray();
            }

            dd(array_values($device_token));


            echo " [x] Done\n";
        };

        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }
//
//        $client = new GuzzleClient([
//            'base_uri' => 'https://fcm.googleapis.com/fcm/send',
//        ]);
////        $payload = file_get_contents('/my-data.xml');
//        $response = $client->post('', [
//            'body' => [
//                'to' => $deviceToken,
//                "collapse_key" => "type_a",
//                "notification" => [
//                    "body" => "cua mo",
//                    "title" => "Collapsing A",
//                ],
//                "data" => [
//                    "body" => "First Notification",
//                    "title" => "Collapsing A",
//                    "key_1" => "Data for key one",
//                    "key_2" => "Hellowww"
//                ],
//            ],
//            'headers' => [
//                'content-type' => 'application/json',
//                'Authorization ' => 'key=AIzaSyAbztHNWF15A3PSZ4Z1Won4YHjtRjOA9_M'
//            ]
//        ]);
//        $body = $response->getBody();
//        print_r(json_decode((string)$body));
//
//        while (count($channel->callbacks)) {
//            $channel->wait();
//        }
    }


}
