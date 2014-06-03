<?php
namespace CapSciences\ServerVip\WebsocketBundle\Websockets;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ConnectionData implements \Ratchet\ConnectionInterface
{
    private $uid;

    /**
     * @var \Closure
     */
    private $updatedCallback;

    private $services;

    private $isMonitor = false;

    /**
     * @var \Ratchet\WebSocket\Version\RFC6455\Connection
     */
    private $conn;

    /**
     * @var string
     */
    private $macAddress;

    /** ------ DATA ------ */

    private $peripherique;

    private $visite;

    private $flotte;

    private $lastHeartbeat;

    public function __construct($uid, \Ratchet\WebSocket\Version\RFC6455\Connection $conn)
    {
        $this->uid = $uid;
        $this->conn = $conn;
        $this->services = array();
        $this->lastHeartbeat = time();
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    public function heartbeat()
    {
        $this->lastHeartbeat = time();
        return $this;
    }

    public function getLastHeartbeatSince()
    {
        return time() - $this->lastHeartbeat;
    }

    /**
     * @return int
     */
    public function getLastHeartbeatText()
    {
        return date('d/m/Y H:i:s', $this->lastHeartbeat);
    }

    /**
     * @param callable $callback
     */
    public function setUpdatedCallback(\Closure $callback)
    {
        $this->updatedCallback = $callback;
    }

    /**
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->conn->getRemoteAddress();
    }

    public function addService($service)
    {
        $this->services[$service] = $service;
    }

    public function removeService($service)
    {
        unset($this->services[$service]);
    }

    public function getServices()
    {
        return array_values($this->services);
    }

    /**
     * @param $mac
     */
    public function setMacAddress($macAddress)
    {
        $this->macAddress = $macAddress;
        $this->monitorUpdatedStatus();
        return $this;
    }

    /**
     * @return string
     */
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    public function setPeripherique($peripherique)
    {
        $this->peripherique = $peripherique;
        $this->monitorUpdatedStatus();
        return $this;
    }

    public function getPeripherique()
    {
        return $this->peripherique;
    }

    /**
     * @param $visite
     * @return ConnectionData
     */
    public function setVisite($visite)
    {
        $this->visite = $visite;
        $this->monitorUpdatedStatus();
        return $this;
    }

    public function monitorUpdatedStatus() {
        $callback = $this->updatedCallback;
        $callback($this);
    }

    /**
     * @return mixed
     */
    public function getVisite()
    {
        return $this->visite;
    }

    /**
     * @param boolean $isMonitor
     */
    public function setIsMonitor($isMonitor)
    {
        $this->isMonitor = $isMonitor;
    }

    /**
     * @return boolean
     */
    public function getIsMonitor()
    {
        return $this->isMonitor;
    }

    /**
     * Send data to the connection
     * @param string
     * @return \Ratchet\ConnectionInterface
     */
    public function send($data)
    {
        $this->conn->send($data);
    }

    /**
     * Close the connection
     */
    public function close($code = 1000)
    {
        $this->conn->close($code);
    }


    public function __toString() {
        return sprintf("%s %s (%s)", $this->getUid(), $this->getRemoteAddress(), $this->getMacAddress());
    }
}
