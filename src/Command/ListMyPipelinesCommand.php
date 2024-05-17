<?php

/**
 * List my pipeline data
 */

namespace Ringli\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Responsible for listing my pipelines
 */
class ListMyPipelinesCommand extends PipelineCommand
{
    /**
     * Configure the command with options.
     */
    protected function configure(): void
    {
        $this
            ->setName('mine')
            ->setDescription('List my pipelines')
            ->addArgument('repo', InputArgument::REQUIRED, 'The repo to filter pipelines for');
    }

    /**
     * Let subclasses decide the API call.
     *
     * @return array<mixed>
     */
    protected function getPipelines(InputInterface $input): array
    {
        return $this->client->getMyPipelines($input->getArgument('repo'));
    }
}
