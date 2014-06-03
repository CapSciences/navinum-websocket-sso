<?php
namespace CapSciences\ServerVip\WebsocketBundle\Websockets\Handler;

use Ratchet\ConnectionInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use CapSciences\ServerVip\WebsocketBundle\Websockets\MessageHandler;
use CapSciences\ServerVip\WebsocketBundle\Websockets\ConnectionData;

use CapSciences\ServerVip\NavinumBundle\Providers\Visite as VisiteProvider;
use CapSciences\ServerVip\NavinumBundle\Providers\Peripherique as PeripheriqueProvider;

class CoreHandler extends AbstractHandler
{
    public function __construct()
    {
    }

    public function handshakeAction(ConnectionData $conn, $data)
    {
        $peripherique = $this->getPeripheriqueProvider()->findOneByMacAddress($data->mac_address);

        $conn->setPeripherique($peripherique);
        $conn->setMacAddress($data->mac_address);

        $msg                      = new \stdClass();
        $msg->command             = "servervip2.handshake";
        $msg->data                = new \stdClass();
        $msg->data->connection_id = $conn->getUid();
        $this->messageHandler->send($conn, $msg);
    }

    public function heartbeatAction(ConnectionData $conn, $data)
    {
        $conn->heartbeat();
        $conn->monitorUpdatedStatus();
    }

    public function resetedAction(ConnectionData $conn, $data)
    {
        $conn->setVisite(null);
    }

    public function resetAction(ConnectionData $conn, $data)
    {
        $uid = explode(',', $data->dest);
        $to = array();

        foreach ($this->messageHandler->getClients() as $client) {
            /** @var $client ConnectionData */
            if (in_array($client->getUid(), $uid)) {
                $to[] = $client;
                $client->setVisite(null);
                break;
            }
        }

        if(count($to)) {
            // Notify les périphérique que le visiteur est parti ailleurs
            // Prepare message
            $msgNotif                = new \stdClass();
            $msgNotif->command       = "servervip2.notification";
            $msgNotif->data          = new \stdClass();
            $msgNotif->data->type    = 'core:reset';
            $msgNotif->data->options = new \stdClass();

            $this->messageHandler->broadcast($to, $msgNotif);
        }
    }

    public function updateZmqAction($data)
    {
        if ($data->model == 'peripherique') {
            foreach ($this->messageHandler->getClients() as $client) {
                /** @var $client ConnectionData */
                if ($client->getMacAddress() == $data->object->adresse_mac) {
                    $client->setPeripherique($data->object);
                    break;
                }
            }
        }
    }

    public function authZmqAction($data)
    {
        $visiteId     = $data->visite_id;
        $connectionId = $data->connection_id;

        $newDone = false;
        $oldDone = false;

        foreach ($this->messageHandler->getClients() as $client) {
            /** @var $client ConnectionData */
            if ($client->getUid() == $connectionId) {
                $client->setVisite($this->getVisiteProvider()->find($visiteId));
                $newDone = true;

                if ($newDone && $oldDone) break;
            } elseif ($client->getVisite() != null && $client->getVisite()->guid == $visiteId) {
                $client->setVisite(null);

                // Notify les périphérique que le visiteur est parti ailleurs
                // Prepare message
                $msgNotif                = new \stdClass();
                $msgNotif->command       = "servervip2.notification";
                $msgNotif->data          = new \stdClass();
                $msgNotif->data->type    = 'core:reset';
                $msgNotif->data->options = new \stdClass();

                $this->messageHandler->send($client, $msgNotif);

                $oldDone = true;
                if ($newDone && $oldDone) break;
            }
        }
    }

    public function resetZmqAction($data)
    {
        $visiteId     = $data->visite_id;

        foreach ($this->messageHandler->getClients() as $client) {
            /** @var $client ConnectionData */
            if ($client->getVisite() != null && $client->getVisite()->guid == $visiteId) {
                $client->setVisite(null);

                // Notify les périphérique que le visiteur est parti ailleurs
                // Prepare message
                $msgNotif                = new \stdClass();
                $msgNotif->command       = "servervip2.notification";
                $msgNotif->data          = new \stdClass();
                $msgNotif->data->type    = 'core:reset';
                $msgNotif->data->options = new \stdClass();

                $this->messageHandler->send($client, $msgNotif);
                break;
            }
        }
    }

    public function keepaliveZmqAction($data)
    {
        $msg          = new \stdClass();
        $msg->command = 'core.heartbeat';
        $msg->data    = new \stdClass();

        $toKick = array();

        foreach ($this->messageHandler->getClients() as $client) {
            /** @var $client ConnectionData */
            if ($client->getLastHeartbeatSince() >= 120) {
                $toKick[] = $client;
            } else {
                $this->messageHandler->send($client, $msg);
            }
        }

        // Kick timedout
        foreach ($toKick as $client) {
            /** @var $client ConnectionData */
            $client->close(1001);
        }
    }

    /**
     * @return \CapSciences\ServerVip\WebsocketBundle\IO\IOInterface
     */
    private function getOutput()
    {
        return $this->messageHandler->getOuput();
    }
}
