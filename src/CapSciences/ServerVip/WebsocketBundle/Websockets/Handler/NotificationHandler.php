<?php
namespace CapSciences\ServerVip\WebsocketBundle\Websockets\Handler;

use Ratchet\ConnectionInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use CapSciences\ServerVip\WebsocketBundle\Websockets\MessageHandler;
use CapSciences\ServerVip\WebsocketBundle\Websockets\ConnectionData;

use CapSciences\ServerVip\WebsocketBundle\Providers\Visite as VisiteProvider;

class NotificationHandler extends AbstractHandler
{
    public function __construct()
    {
    }

    public function sendAction($conn, $data)
    {
        $this->forwardNotification($data);
    }

    public function sendZmqAction($data)
    {
        $this->forwardNotification($data);
    }

    private function forwardNotification($data)
    {
        // Prepare message
        $msgNotif = new \stdClass();
        $msgNotif->command = "servervip2.notification";
        $msgNotif->data = new \stdClass();
        $msgNotif->data->type = $data->type;
        $msgNotif->data->options = $data->options;

        $dests = $this->parseDestNotification($data->dest);

        // Save notification
        if (in_array($data->type, array('general:notif'))) {
            $this->saveNotification($data->dest, $data);
        }

        // Search Dest
        $this->messageHandler->broadcast($dests, $msgNotif);
    }

    private function parseDestNotification($dest)
    {
        $to = array();

        if (!is_array($dest)) {
            $dest = array($dest);
        }

        if (count($dest) == 1 && $dest == '*') {
            foreach ($this->messageHandler->getClients() as $client) {
                $to[] = $client;
            }
            return $to;
        }

        foreach ($dest as $name) {
            list($type, $uid) = explode(':', $name);

            switch ($type) {
                case 'conn':
                    foreach ($this->messageHandler->getClients() as $client) {
                        /** @var $client ConnectionData */
                        if ($client->getUid() == $uid) {
                            $to[] = $client;
                            break;
                        }
                    }
                    break;
                case 'visiteur':
                    foreach ($this->messageHandler->getClients() as $client) {
                        /** @var $client ConnectionData */
                        if ($client->getVisite() != null && $client->getVisite()->visiteur_id == $uid) {
                            $to[] = $client;
                            break;
                        }
                    }
                    break;
                case 'visite':
                    foreach ($this->messageHandler->getClients() as $client) {
                        /** @var $client ConnectionData */
                        if ($client->getVisite() != null && $client->getVisite()->guid == $uid) {
                            $to[] = $client;
                            break;
                        }
                    }
                    break;
                case 'exposition':
                    foreach ($this->messageHandler->getClients() as $client) {
                        /** @var $client ConnectionData */
                        if ($client->getVisite() != null && $client->getVisite()->exposition_id == $uid) {
                            $to[] = $client;
                        }
                    }
                    break;
                case 'parcours':
                    foreach ($this->messageHandler->getClients() as $client) {
                        /** @var $client ConnectionData */
                        if ($client->getVisite() != null && $client->getVisite()->parcours_id == $uid) {
                            $to[] = $client;
                        }
                    }
                    break;
            }
        }

        return $to;
    }

    private function saveNotification($dest, $data)
    {

        $parameters = json_encode((array) $data->options);

        if (!is_array($dest)) {
            $dest = array($dest);
        }

        if(!isset($data->model)) {
            $data->model = null;
        }

        foreach ($dest as $name) {
            list($type, $uid) = explode(':', $name);

            switch ($type) {

                case 'visiteur':
                    $visite = $this->getVisiteProvider()->findOneByVisiteur($uid);
                    if ($visite != null) {
                        $this->getNotificationProvider()->create(array(
                            'libelle' => $data->options->title,
                            'visiteur_id' => $uid,
                            'visite_id' => $visite->guid,
                            'from_model' => $data->model != null ? $data->model->model : '',
                            'from_model_id' => $data->model != null ? $data->model->model_id : '',
                            'parameter' => $parameters

                        ));
                    }
                    break;
                case 'visite':
                    $visite = $this->getVisiteProvider()->find($uid);
                    if ($visite != null) {
                        $this->getNotificationProvider()->create(array(
                            'libelle' => $data->options->title,
                            'visiteur_id' => $visite->visite_id,
                            'visite_id' => $visite->guid,
                            'from_model' => $data->model != null ? $data->model->model : '',
                            'from_model_id' => $data->model != null ? $data->model->model_id : '',
                            'parameter' => $parameters

                        ));
                    }
                    break;
                case 'exposition':
                    foreach ($this->messageHandler->getClients() as $client) {
                        /** @var $client ConnectionData */
                        if ($client->getVisite() != null && $client->getVisite()->exposition_id == $uid) {
                            $this->getNotificationProvider()->create(array(
                                'libelle' => $data->options->title,
                                'visiteur_id' => $client->getVisite()->visiteur_id,
                                'visite_id' => $client->getVisite()->guid,
                                'from_model' => $data->model != null ? $data->model->model : '',
                                'from_model_id' => $data->model != null ? $data->model->model_id : '',
                                'parameter' => $parameters

                            ));
                        }
                    }
                    break;
                case 'parcours':
                    foreach ($this->messageHandler->getClients() as $client) {
                        /** @var $client ConnectionData */
                        if ($client->getVisite() != null && $client->getVisite()->parcours_id == $uid) {
                            $this->getNotificationProvider()->create(array(
                                'libelle' => $data->options->title,
                                'visiteur_id' => $client->getVisite()->visiteur_id,
                                'visite_id' => $client->getVisite()->guid,
                                'from_model' => $data->model != null ? $data->model->model : '',
                                'from_model_id' => $data->model != null ? $data->model->model_id : '',
                                'parameter' => $parameters

                            ));
                        }
                    }
                    break;
            }
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
