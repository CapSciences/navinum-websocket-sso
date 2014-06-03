<?php
namespace CapSciences\ServerVip\WebsocketBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\FlashPolicy;
use Ratchet\Server\IoServer;
use CapSciences\ServerVip\WebsocketBundle\WebSockets\MessageHandler;
use CapSciences\ServerVip\WebsocketBundle\IO\OutputConsole;
use CapSciences\ServerVip\WebsocketBundle\IO\IOInterface;
use CapSciences\ServerVip\WebsocketBundle\Websockets\WebsocketRouter;

class WebsocketKeepAliveCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('servervip2:websocket:keepalive')
            ->setDescription('Force websocket server to check availability of its clients')
            ->setHelp('
servervip2:websocket:keepalive
Start home daemon
');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
         // This is our new stuff
        $output->writeln("init zmq context");
        $context = new \ZMQContext();
        $output->writeln("get socket");
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'ServerVip pusher');
        $socket->setSockOpt(\ZMQ::SOCKOPT_SNDTIMEO, 5);
        $socket->setSockOpt(\ZMQ::SOCKOPT_LINGER, 0);

        $output->writeln(sprintf('Try to connect to tcp://%s:%s', $this->getZmqListeningInterface(), $this->getZmqPort()));
        $socket->connect(sprintf('tcp://%s:%s', $this->getZmqListeningInterface(), $this->getZmqPort()));
        $output->writeln("connected");


        $socket->send(json_encode(array(
            'command' => 'core.keepalive',
            'data' => array()
        )));

        $output->writeln("end");
    }

    private function getZmqPort()
    {
        if ($this->getContainer()->hasParameter('zmq_port')) {
            return $this->getContainer()->getParameter('zmq_port');
        }
        return 8001;
    }

    private function getZmqListeningInterface()
    {
        if ($this->getContainer()->hasParameter('zmq_ip')) {
            return $this->getContainer()->getParameter('zmq_ip');
        }
        return '127.0.0.1';
    }

}
