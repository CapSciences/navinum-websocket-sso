<?php
namespace CapSciences\ServerVip\WebsocketBundle\IO;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

class OutputLogger implements IOInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function error($message, $exception = null)
    {
        $this->logger->err($message);
    }

    public function warning($message)
    {
        $this->logger->warn($message);
    }

    public function info($message)
    {
        $this->logger->info($message);
    }

    public function debug($message)
    {
        $this->logger->debug($message);
    }
}
