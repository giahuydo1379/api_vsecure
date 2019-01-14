<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class PushNotifyExample extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushNotify:send';

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


        $argv = [
            'address_mac' => "DC4F228ABB6F",
            'home_away' => 0,
            'alarm_door_bell' => 0,
            'battery' => 3,
            'arming_dis_arming' => 1,
            'door_status' => 0,
            'alarm_volume' => 1,
            'arm_delay' => 5,
            'alarm_delay' => 5,
            'alarm_duration' => 60,
            'self_check_mode' => 0

        ];

        $argv = json_encode($argv);
//        dd($argv);


        $msg = new AMQPMessage($argv);

        $channel->basic_publish($msg, '', 'hello');

        echo ' [x] Sent ', "\n";
    }

}
