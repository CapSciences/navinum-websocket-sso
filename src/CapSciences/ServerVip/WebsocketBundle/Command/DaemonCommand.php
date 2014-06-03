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
use CapSciences\ServerVip\WebsocketBundle\IO\OutputLogger;

class DaemonCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('servervip2:daemon')
            ->addOption('pidFile', 'p', InputOption::VALUE_OPTIONAL, 'pid file', '/var/run/servervip2-daemon.pid')
            ->addOption('daemon', null, InputOption::VALUE_NONE, 'start the daemon if stopped')
            ->addOption('stop', null, InputOption::VALUE_NONE, 'stop the daemon if started')
            ->addOption('restart', null, InputOption::VALUE_NONE, 'restart the daemon if started')
            ->setDescription('Start home daemon')
            ->setHelp('
home:daemon
Start home daemon
');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pidFile = $input->getOption('pidFile');

        if ($input->getOption('stop')) {
            $this->stopDaemon($pidFile, $output);
        } elseif ($input->getOption('daemon')) {
            $this->startDaemon($pidFile, $output);
        } elseif ($input->getOption('restart')) {
            if ($this->stopDaemon($pidFile, $output)) {
                $this->startDaemon($pidFile, $output);
            }
        } else {
            $this->worker(new OutputConsole($output));
        }
    }

    private function worker(IOInterface $output)
    {
        $loop = \React\EventLoop\Factory::create();

        /** @var $handler \CapSciences\ServerVip\WebsocketBundle\Websockets\MessageHandler */
        $handler = $this->getMessageHandler();
        $handler->setIOInterface($output);


        $webSock = new \React\Socket\Server($loop);
        $webSock->listen($this->getWebsocketPort(), $this->getWebsocketListeningInterface());
        //$webSock->listen(843,  $this->getWebsocketListeningInterface());

        $router = new WebsocketRouter($handler, $this->getWebsocketPort(), $output);
        $webserver = new IoServer($router, $webSock);

        $context = new \React\ZMQ\Context($loop);
        $pull    = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://'.$this->getZmqListeningInterface().':'.$this->getZmqPort());
        $pull->on('message', array($handler, 'onZmqPush'));

        $output->info('Server started...');
        $loop->run();
    }

    private function startDaemon($pidFile, OutputInterface $output)
    {
        if ($this->checkIsRunning($pidFile)) {
            $output->writeln(array(
                '<error></error>',
                '<error>Servervip2 daemon seems allready started</error>',
                '<error></error>'
            ));
            die();
        }

        $pid = pcntl_fork();
        if ($pid == -1) {
            syslog(1, 'Unable to start Servervip2 as a daemon');
            $output->writeln('Unable to start Servervip2 as a daemon');
            die();
        } elseif ($pid) {
            // Le père
            file_put_contents($pidFile, $pid);
            $output->writeln('Servervip2 started as a daemon');
            die();
        }
        // Le fils
        $ouputLogger = new OutputLogger($this->getContainer()->get('logger'));
        $this->worker($ouputLogger);
    }

    private function stopDaemon($pidFile, OutputInterface $output)
    {
        if ($this->checkIsRunning($pidFile)) {
            $pid = file_get_contents($pidFile);
            $p   = new \Symfony\Component\Process\Process('kill -9 ' . $pid);
            $p->run();
            if ($p->isSuccessful()) {
                $output->writeln('<info>Servervip2 daemon stopped</info>');

                return true;
            } else {
                $output->writeln('<error>Unable to stop servervip2 daemon</error>');
                die();
            }
        }

        $output->writeln('<error>Presenter not running</error>');

        return true;
    }

    private function checkIsRunning($pidFile)
    {
        if (!file_exists($pidFile)) {
            return false;
        }

        $pid = file_get_contents($pidFile);
        $p   = new \Symfony\Component\Process\Process('ls /proc/' . $pid);
        $p->run();
        if (!$p->isSuccessful()) {
            // Remove the pidFile
            unlink($pidFile);

            return false;
        }

        return true;
    }

    /**
     * @return \CapSciences\ServerVip\WebsocketBundle\Websockets\MessageHandler
     */
    private function getMessageHandler()
    {
        return $this->getContainer()->get("servervip2_websocket.websockets.message_handler");
    }

    private function getWebsocketListeningInterface()
    {
        if ($this->getContainer()->hasParameter('websocket_ip')) {
            return $this->getContainer()->getParameter('websocket_ip');
        }
        return '0.0.0.0';
    }

    private function getWebsocketPort()
    {
        if ($this->getContainer()->hasParameter('websocket_port')) {
            return $this->getContainer()->getParameter('websocket_port');
        }
        return 8000;
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
