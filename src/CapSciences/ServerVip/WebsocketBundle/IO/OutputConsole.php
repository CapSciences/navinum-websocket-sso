<?php
namespace CapSciences\ServerVip\WebsocketBundle\IO;

use Symfony\Component\Console\Output\OutputInterface;

class OutputConsole implements IOInterface
{
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function error($message, $exception = null)
    {
        $this->output->writeln(array(
            '<error></error>',
            '<error>'.$message.'</error>',
            '<error></error>'
        ));
    }

    public function warning($message)
    {
        $this->output->writeln(array(
            '<warning>'.$message.'</warning>',
        ));
    }

    public function info($message)
    {
        $this->output->writeln(array(
            '<info>'.$message.'</info>',
        ));
    }

    public function debug($message)
    {
        $this->output->writeln($message);
    }
}
