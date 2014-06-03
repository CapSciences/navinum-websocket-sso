<?php
namespace CapSciences\ServerVip\WebsocketBundle\WebSockets\Handler;

use Symfony\Component\DependencyInjection\Container;
use Ratchet\ConnectionInterface;
use CapSciences\ServerVip\WebsocketBundle\Websockets\MessageHandler;
use CapSciences\ServerVip\WebsocketBundle\Websockets\ConnectionData;

abstract class AbstractHandler
{
    /**
     * @var MessageHandler
     */
    protected $messageHandler;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $serviceContainer;

    public function setMessageHandler(MessageHandler $messageHandler)
    {
        $this->messageHandler = $messageHandler;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Container $serviceContainer
     */
    public function setServiceContainer(Container $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function onClientJoin(ConnectionData $conn)
    {
    }

    public function onClientLeave(ConnectionData $conn)
    {
    }

    /**
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\Visite
     */
    protected function getVisiteProvider()
    {
        return $this->getProvider('visite');
    }

    /**
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\Rfid
     */
    protected function getRfidProvider()
    {
        return $this->getProvider('rfid');
    }

    /**
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\Visiteur
     */
    protected function getVisiteurProvider()
    {
        return $this->getProvider('visiteur');
    }

    /**
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\Peripherique
     */
    protected function getPeripheriqueProvider()
    {
        return $this->getProvider('peripherique');
    }

    /**
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\Flotte
     */
    protected function getFlotteProvider()
    {
        return $this->getProvider('flotte');
    }

    /**
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\Interactif
     */
    protected function getInteractifProvider()
    {
        return $this->getProvider('interactif');
    }

    /**
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\Notification
     */
    protected function getNotificationProvider()
    {
        return $this->getProvider('notification');
    }


    /**
     * @param $name
     * @return \CapSciences\ServerVip\NavinumBundle\Providers\ApiProvider
     */
    private function getProvider($name)
    {
        return $this->serviceContainer->get('cap_sciences_server_vip_navinum.providers.' . $name);
    }
}
