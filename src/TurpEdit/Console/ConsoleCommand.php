<?php

namespace TurpEdit\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleCommand extends Command
{
    use ConsoleTrait;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setupConsole($input, $output);
        $this->serve();
    }
    /**
     *
     */
    protected function serve()
    {
    }
    protected function displayGPMRelease()
    {
        $this->output->writeln('');
        $this->output->writeln('TurpEdit Console');
        $this->output->writeln('');
    }
}