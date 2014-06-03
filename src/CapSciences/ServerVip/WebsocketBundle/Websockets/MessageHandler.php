<?php
namespace CapSciences\ServerVip\WebsocketBundle\Websockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;

use CapSciences\ServerVip\WebsocketBundle\IO\IOInterface;
use CapSciences\ServerVip\WebsocketBundle\Websockets\Handler\AbstractHandler;

class MessageHandler implements MessageComponentInterface
{
    /**
     * @var IOInterface
     */
    protected $output;

    /** @var Registry */
    protected $doctrine;

    protected $handlers;

    protected $default;

    protected $uids;

    /**
     * @var \SplObjectStorage
     */
    protected $clients;

    public function __construct(Registry $doctrine, $default)
    {
        $this->clients = new \SplObjectStorage;
        $this->uids = array();
        $this->handlers = array();
        $this->default = $default;
        $this->doctrine = $doctrine;
    }

    public function setIOInterface(IOInterface $output)
    {
        $this->output = $output;
    }

    public function addHandler($key, AbstractHandler $object)
    {
        if (array_key_exists($key, $this->handlers)) {
            throw new \Exception(sprintf('An handler is allready bind to namespace "%s"', $key));
        }

        $this->handlers[$key] = $object;
        $object->setMessageHandler($this);
    }

    final public function onOpen(ConnectionInterface $conn)
    {
        try {
            $this->output->info('client join from ' . $conn->getRemoteAddress());

            do {
                $uid = time();
            } while (in_array($uid, $this->uids));
            $this->uids[] = $uid;

            $data = new ConnectionData($uid, $conn);
            $this->clients->attach($conn, $data);

            foreach ($this->handlers as $handler) {
                /** @var $handler AbstractHandler */
                $handler->onClientJoin($data);
            }

        } catch (\Exception $e) {
            $this->output->error((string)$e);
        }
    }

    final public function onMessage(ConnectionInterface $from, $rawMsg)
    {
        if (!$this->clients->contains($from)) {
            $this->output->debug('Reject message from an unknow client');

            return;
        }

        $this->getOuput()->debug("receive json message : " . $rawMsg);

        $msg = json_decode($rawMsg);

        if (is_null($msg)) {
            // ignore message
            $this->output->error('Client send bad data');

            return;
        }

        if ($msg->command == 'core.sendWithCallback') {

            $subMessage = new \stdClass();
            $subMessage->command = $msg->data->command;
            $subMessage->data = $msg->data->data;

            $self = $this;
            $this->callAction($from, $subMessage, function ($data) use ($msg, $from, $self) {
                /** @var $self MessageHandler */

                $response = new \stdClass();
                $response->command = 'core.callback';
                $response->callbackId = $msg->data->callbackId;
                $response->data = $data;

                $self->send($from, $response);
            });
        } else {
            $this->callAction($from, $msg);
        }
    }

    final public function onClose(ConnectionInterface $conn)
    {
        try {
            /** @var $clientData ConnectionData */
            $clientData = $this->clients[$conn];

            $reverse = array_flip($this->uids);
            unset($this->uids[$reverse[$clientData->getUid()]]);

            foreach ($this->handlers as $handler) {
                /** @var $handler AbstractHandler */
                $handler->onClientLeave($clientData);
            }

            $this->clients->detach($conn);
            $this->output->info('client leave from ' . $conn->getRemoteAddress());
        } catch (\Exception $e) {
            $this->output->error((string)$e->__toString());
        }
    }

    final public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->output->error((string)$e);
        $conn->close();
    }

    final public function broadcastAll($msg)
    {
        $this->broadcast($this->clients, $msg);
    }

    final public function broadcastOthers($conn, $msg)
    {
        $preparedMessage = $this->prepareMessage($msg);

        foreach ($this->clients as $other) {
            if ($other != $conn) {
                $this->doSend($other, $preparedMessage);
            }
        }
    }

    final public function broadcast($recipients, $msg)
    {
        $preparedMessage = $this->prepareMessage($msg);

        foreach ($recipients as $conn) {
            $this->doSend($conn, $preparedMessage);
        }
    }

    final public function send(ConnectionInterface $conn, $msg)
    {
        $this->doSend($conn, $this->prepareMessage($msg));
    }

    /**
     * @return IOInterface
     */
    public function getOuput()
    {
        return $this->output;
    }

    private function prepareMessage($msg)
    {
        if ($msg instanceof Message) {
            /** @var $msg Message */

            return $msg->toJson();
        } else {
            return json_encode($msg);
        }
    }

    private function doSend(ConnectionInterface $conn, $preparedMessage)
    {
        $this->getOuput()->debug('send data to ' . $conn->getRemoteAddress() . ' : ' . $preparedMessage);

        $conn->send($preparedMessage);
    }

    private function callAction(ConnectionInterface $from, \stdClass $msg, $callback = null)
    {
        // Decode message scope
        $commandParts = explode('.', $msg->command);
        $handler = null;

        if (count($commandParts) == 2) {
            $handler = $this->handlers[$commandParts[0]];
            $msg->command = $commandParts[1];
        }

        if (!$handler && isset($this->handlers[$this->default])) {
            $handler = $this->handlers[$this->default];
        }

        if (!$handler) {
            $this->output->warning(sprintf('Handler not found for %s.', $msg->command));
            return null;
        }

         $this->clients[$from]->heartbeat();

        $controler = $msg->command . 'Action';
        if (method_exists($handler, $controler)) {
            try {
                return call_user_func(array($handler, $controler), $this->clients[$from], $msg->data, $callback);
            } catch (\Exception $e) {
                $this->output->error('An error occured in handler \"' . get_class($handler) . '::' . $controler . '\" : ' . $e->__toString());
            }
        } else {
            $this->output->warning(sprintf('Handler %s::%s does not exist.', get_class($handler), $controler));
        }

        return null;
    }

    /**
     * @param  \Ratchet\ConnectionInterface $conn
     * @return ConnectionData
     */
    public function getConnectionData(ConnectionInterface $conn)
    {
        return $this->clients[$conn];
    }

    public function onZmqPush($data)
    {
        $msg = json_decode($data);

        // Decode message scope
        $commandParts = explode('.', $msg->command);

        $handler = null;
        if (isset($this->handlers[$this->default])) {
            $handler = $this->handlers[$this->default];
        }

        if (count($commandParts) == 2) {
            if (isset($this->handlers[$commandParts[0]])) {
                $handler = $this->handlers[$commandParts[0]];
                $msg->command = $commandParts[1];
            } else {
                $this->output->debug(print_r($msg, true));
                $this->output->warning(sprintf('Namespace %s not registred', $commandParts[0]));
                return;
            }
        }

        $controler = $msg->command . 'ZmqAction';
        if (method_exists($handler, $controler)) {
            try {
                return call_user_func(array($handler, $controler), $msg->data);
            } catch (\Exception $e) {
                $this->output->error('An error occured in handler \"' . get_class($handler) . '::' . $controler . '\" : ' . $e->__toString());
            }
        } else {
            $this->output->warning(sprintf('Handler %s::%s does not exist.', get_class($handler), $controler));
        }

        return null;
    }

    public function getClients()
    {
        $clients = array();
        foreach ($this->clients as $c) {
            $clients[] = $this->clients[$c];
        }
        return $clients;
    }
}
