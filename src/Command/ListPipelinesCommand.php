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

        $output->writeln("Report for " . $this->client->getOrg());

        $table = new Table($output);
        $table->setHeaders(['Project', 'Who', 'Branch', 'Status', 'Started']);

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
            $slug = preg_replace('|' . $this->client->getOrg() . "/" . '|i', "", $slug);
            $status = $workflow['status'];
            $started = (string) ($workflow['created_at'] ?? "");
            $startTime = \DateTime::createFromFormat(\DateTime::ISO8601, $started);

            $row = $this->decorateRowByStatus($status, [
                "<href=https://app.circleci.com/pipelines/${slug}/${number}>${slug}:${number}</>",
                $actor,
                $branch,
                $status,
                $startTime != false ? $startTime->format("Y-m-d H:i:s") : $started,
            ]);

            $table->addRow($row);
        }
        $table->render();
        return 0;
    }

    /**
     * Decorate the entire row entry by the status
     *
     * @param string $status The status of the workflow
     * @param array<mixed> $row The row we want to decorate, via the status
     *
     * @return array<mixed>
     */
    protected function decorateRowByStatus(string $status, array $row): array
    {
        foreach ($row as &$item) {
            switch ($status) {
                case Status::RUNNING:
                    $item = "<fg=blue>${item}</>";
                    break;

                case Status::SUCCESS:
                    $item = "<fg=green>${item}</>";
                    break;

                case Status::FAILING:
                case Status::FAILED:
                    $item = "<fg=red>${item}</>";
                    break;
            }
        }

        return $row;
    }
}
