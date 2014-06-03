<?php
namespace CapSciences\ServerVip\WebsocketBundle\Websockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\FlashPolicy;

use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\ServerProtocol;

use CapSciences\ServerVip\WebsocketBundle\Websockets\MessageHandler;

class WebsocketRouter implements MessageComponentInterface
{
    private $policy;
    private $websocketServer;

    /**
     * @var \CapSciences\ServerVip\WebsocketBundle\IO\IOInterface
     */
    private $ioConsole;

    public function __construct(MessageHandler $messageHandler, $websocketPort, \CapSciences\ServerVip\WebsocketBundle\IO\IOInterface $ioConsole)
    {
        $this->ioConsole = $ioConsole;

        //Setup Flash Server
        $this->policy = new FlashPolicy;
        $this->policy->addAllowedAccess('*', $websocketPort);

        //Setup Wamp Server
        $this->websocketServer = new WsServer($messageHandler);
    }


    public function onOpen(ConnectionInterface $conn)
    {
        //Set default route to the wamp server
        $conn->route = $this->websocketServer;
        $conn->route->onOpen($conn);

        //Set first message to true
        $conn->firstMessage = true;
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        //If first message is true check if we have a policy file request
        if ($from->firstMessage) {
            $from->firstMessage = false;
            $flashPolicyRequest = strpos($msg, 'policy-file-request');
            if ($flashPolicyRequest !== false) {
                //Send an onclose to websocket class
                $from->route->onClose($from);

                //Change route to flash policy
                $from->route = $this->policy;
            }
        }

        $from->route->onMessage($from, $msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        if (isset($conn->route)) {
            $conn->route->onClose($conn);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $conn->route->onError($conn, $e);
    }
}