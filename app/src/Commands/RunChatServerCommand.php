<?php

namespace App\Commands;

use App\Chat\Chat;
use Core\BaseCommand;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

class RunChatServerCommand extends BaseCommand
{
    /**
     * The command signature.
     *
     * @var string
     */
    private $signature = "serve:chat";

    /**
     * The command description.
     *
     * @var string
     */
    private $description = "Run chat server";

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct($this->signature, $this->description);
    }

    /**
     * To be call after execute the command.
     *
     * @param  Input $input
     * @param  Output $output
     * @return void
     */
    public function handle(Input $input, Output $output)
    {
        $config = config('chat');

        $host = $config['host'];
        $port = $config['port'];

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat
                )
            ),
            $port,
            $host
        );

        $output->writeln("Listening {$host}:{$port} ...");

        $server->run();
    }
}
