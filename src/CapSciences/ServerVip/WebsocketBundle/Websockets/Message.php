<?php
namespace CapSciences\ServerVip\WebsocketBundle\Websockets;

/**
 *
 * @property $command
 * @property $data
 */
class Message
{

    private $command;
    private $data;

    public function __construct($command, $data = null)
    {
        $this->command = $command;
        if (is_null($data)) {
            $this->data = new \stdClass();
        } else {
            $this->data = $data;
        }
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public static function createFromArray($command, $data)
    {
        $msg = new self($command);
        foreach ($data as $key => $value) {
            $msg->data->$key = $value;
        }

        return $msg;
    }

    public function toJson()
    {
        return json_encode(array('command' => $this->command, 'data' => $this->data));
    }

}
