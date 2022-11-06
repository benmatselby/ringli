<?php

/**
 * List the pipelines
 */

namespace Ringli\Command;

use Ringli\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Responsible for listing the pipelines
 */
class ListPipelinesCommand extends Command
{
    /**
     * The Ringli client.
     */
    private Client $client;

    /**
     * Constructor for the command.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    /**
     * Configure the command with options.
     */
    protected function configure(): void
    {
        $this
            ->setName('list')
            ->setDescription('List all the pipelines');
    }

    /**
     * Execute the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->client->getPipelines();
        var_export($data);
        // $output->writeln($data);
        return 0;
    }
}
