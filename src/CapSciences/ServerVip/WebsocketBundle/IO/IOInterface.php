<?php
namespace CapSciences\ServerVip\WebsocketBundle\IO;

interface IOInterface
{
    public function error($message, $exception = null);

    public function warning($message);

    public function info($message);

    public function debug($message);

}
