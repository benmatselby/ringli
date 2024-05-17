<?php

/**
 * List the pipelines
 */

namespace Ringli\Command;

use Ringli\Client;
use Ringli\Workflow\Status;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Responsible for listing the pipelines
 */
class ListPipelinesCommand extends PipelineCommand
{
    /**
     * Configure the command with options.
     */
    protected function configure(): void
    {
        $this
            ->setName('pipelines')
            ->setDescription('List all the pipelines');
    }


    /**
     * Let subclasses decide the API call.
     *
     * @return array<mixed>
     */
    protected function getPipelines(InputInterface $input): array
    {
        return $this->client->getPipelines();
    }
}
