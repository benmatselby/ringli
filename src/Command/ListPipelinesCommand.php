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
        $pipelines = $this->client->getPipelines();

        $table = new Table($output);
        $table->setHeaders(['Project', 'Who', 'Branch', 'Build', 'Status', 'Started']);

        foreach ($pipelines['items'] as $pipeline) {
            $workflowData = $this->client->getWorkflowForPipeline((string) $pipeline['id']);
            $workflows = $workflowData['items'] ?? [];

            if (!isset($workflows[0])) {
                continue;
            }
            $workflow = $workflows[0];

            if (!isset($workflow['pipeline_number'])) {
                continue;
            }

            $actor = $pipeline['trigger']['actor']['login'] ?? "";
            $branch = $pipeline['vcs']['branch'] ?? "";

            $number = (string) ($workflow['pipeline_number'] ?? "");
            $slug = (string) $pipeline['project_slug'];
            $status = $this->getStatus((string) $workflow['status']);
            $started = (string) ($workflow['created_at'] ?? "");

            $row = [
                $slug,
                $actor,
                $branch,
                "<href=https://app.circleci.com/pipelines/${slug}/${number}>${number}</>",
                $status,
                $started,
            ];

            $table->addRow($row);
        }
        $table->render();
        return 0;
    }

    /**
     * Responsible for formatting the status field with a little colour.
     *
     * @param string $status The raw status field.
     *
     * @return string The formatter status field.
     */
    protected function getStatus(string $status): string
    {
        if ($status === "") {
            return "";
        }

        $return = $status;
        switch ($status) {
            case Status::RUNNING:
                $return = "<fg=blue>${status}</>";
                break;

            case Status::SUCCESS:
                $return = "<fg=green>${status}</>";
                break;

            case Status::FAILING:
            case Status::FAILED:
                $return = "<fg=red>${status}</>";
                break;
        }

        return $return;
    }
}
