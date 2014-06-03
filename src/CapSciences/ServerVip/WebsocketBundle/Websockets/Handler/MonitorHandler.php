<?php
namespace CapSciences\ServerVip\WebsocketBundle\Websockets\Handler;

use CapSciences\ServerVip\WebsocketBundle\Websockets\ConnectionData;


class MonitorHandler extends AbstractHandler
{
    /**
     * @var \SplObjectStorage
     */
    protected $monitor;

    public function __construct()
    {
        $this->monitor = new \SplObjectStorage();
    }

    public function onClientJoin(ConnectionData $conn)
    {
        $self = $this;
        $conn->setUpdatedCallback(function (ConnectionData $conn) use ($self) { $self->onUpdateConnectionData($conn); });

        $msgNotif = new \stdClass();
        $msgNotif->command = "monitor.addConnected";
        $msgNotif->data = $this->connectionDataToSendableObject($conn);
        $this->messageHandler->broadcast($this->monitor, $msgNotif);
    }

    public function onClientLeave(ConnectionData $conn)
    {
        if ($this->monitor->contains($conn)) {
            $this->monitor->detach($conn);
        }

        // Inform all monitor of disconnection
        $msgNotif = new \stdClass();
        $msgNotif->command = "monitor.removeConnected";
        $msgNotif->data = new \stdClass();
        $msgNotif->data->uid = $conn->getUid();
        $this->messageHandler->broadcast($this->monitor, $msgNotif);
    }


    public function handshakeAction(ConnectionData $conn, $data)
    {
        $this->monitor->attach($conn);
        $conn->setIsMonitor(true);

        $conn->addService('monitoring');
        $this->getOutput()->debug('client : ' . $conn->getRemoteAddress() . ' handshake for monitoring');

        $msgNotif = new \stdClass();
        $msgNotif->command = "monitor.initialize";
        $msgNotif->data = new \stdClass();
        $msgNotif->data->clients = array();

        foreach ($this->messageHandler->getClients() as $client) {
            /** @var $client ConnectionData */
            $msgNotif->data->clients[] = $this->connectionDataToSendableObject($client);
        }

        $this->messageHandler->send($conn, $msgNotif);
    }

    public function connectionDataToSendableObject(ConnectionData $conn)
    {
        $o = new \stdClass();
        $o->uid = $conn->getUid();
        $o->remoteAddress = $conn->getRemoteAddress();
        $o->macAddress = $conn->getMacAddress();
        if($conn->getPeripherique() != null) {
            $flotte = $this->getFlotteProvider()->find($conn->getPeripherique()->flotte_id);
            $o->flotte =$flotte != null ? $flotte->libelle : '-';
        }
        else {
            $o->flotte = '-';
        }

	//echo('test="'.$conn->getPeripherique().'"');

        if($conn->getPeripherique() != null){
		if($conn->getPeripherique()->interactif_id && !empty($conn->getPeripherique()->interactif_id)){
			$obj = $this->getInteractifProvider()->find($conn->getPeripherique()->interactif_id);
			if($obj){
				$o->interactif = $obj->getLibelle();
			}else{
				$o->interactif = '-';
			}
		}else{
			$o->interactif = '-';
		}
		 //$$o->interactif = $this->getInteractifProvider()->find($conn->getPeripherique()->interactif_id)->getLibelle();
		//$this->getFlotteProvider()->find($conn->getPeripherique()->flotte_id)->libelle;
	}else{
		$o->interactif = '-';
	}
        $o->lastHeartbeat = $conn->getLastHeartbeatText();
        $o->isMonitor = $conn->getIsMonitor();

        $visiteurText = ' - ';
        if($conn->getVisite()){
            $visiteur = $this->getVisiteurProvider()->find($conn->getVisite()->visiteur_id);
            if(!empty($visiteur->email)) {
                $visiteurText = $visiteur->email;
            }
            else {
                $visiteurText = 'Rfid:'.$conn->getVisite()->navinum_id;

            }
        }
        $o->visiteur = $visiteurText;

        return $o;
    }

    public function onUpdateConnectionData(ConnectionData $conn)
    {
        $msgNotif = new \stdClass();
        $msgNotif->command = "monitor.updateConnected";
        $msgNotif->data = $this->connectionDataToSendableObject($conn);
        $this->messageHandler->broadcast($this->monitor, $msgNotif);
    }

    /**
     * @return \CapSciences\ServerVip\WebsocketBundle\IO\IOInterface
     */
    private function getOutput()
    {
        return $this->messageHandler->getOuput();
    }
}
