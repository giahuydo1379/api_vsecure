<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PushReponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushReponse:send';

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
            'huy',
            'co',
            '.',
            '.',
            '.',
            '.',
            '.',
            '.'
        ];

        $data = implode(' ', array_slice($argv, 1));
        // dd($data);
        if (empty($data)) {
            $data = "Hello World!";
        }
        $msg = new AMQPMessage($data);

        $channel->basic_publish($msg, '', 'hello');

        echo ' [x] Sent ', $data, "\n";
    }
}
