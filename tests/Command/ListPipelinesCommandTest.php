<?php

namespace Ringli\Test\Command;

use Ringli\Command\ListPipelinesCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Responsible for testing \Ringli\Command\ListPipelinesCommand
 */
class ListPipelinesCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Ringli\Command\ListPipelinesCommand::configure
     */
    public function testConfigure(): void
    {
        $client = $this->createMock('\Ringli\Client');

        $command = new ListPipelinesCommand($client);

        $this->assertEquals($command->getName(), 'list');
        $this->assertEquals(
            $command->getDescription(),
            'List all the pipelines'
        );
    }

    /**
     * @covers \Ringli\Command\ListPipelinesCommand::execute
     * @dataProvider provideDataForExecute
     *
     * @param array<mixed> $pipeline
     * @param array<mixed> $workflow
     * @param string $expected
     */
    public function testExecuteCanRenderWhatWeWant(array $pipeline, array $workflow, string $expected): void
    {
        $client = $this->createMock('\Ringli\Client');
        $client
            ->method('getPipelines')
            ->willReturn(['items' => [$pipeline]]);

        $client
            ->method('getWorkflowForPipeline')
            ->willReturn(['items' => [$workflow]]);


        $tester = new CommandTester(new ListPipelinesCommand($client));
        $tester->execute([]);

        $this->assertEquals($expected, trim($tester->getDisplay()));
    }

    /**
     * Data provider for testExecuteCanRenderWhatWeWant
     *
     * @return array<mixed>
     */
    protected function provideDataForExecute(): array
    {
        return [
            [
                ['id' => 1, 'project_slug' => 'batman'],
                ['pipeline_number' => 1, 'status' => 'running'],
                <<<END
+----------+-----+--------+---------+---------+
| Project  | Who | Branch | Status  | Started |
+----------+-----+--------+---------+---------+
| batman:1 |     |        | running |         |
+----------+-----+--------+---------+---------+
END
            ],
            [
                [
                    'id' => 5, 'project_slug' => 'batman', 'trigger' =>
                    ['actor' => ['login' => 'robin']], 'vcs' => ['branch' => 'alfred']
                ],
                ['pipeline_number' => 9, 'status' => 'running'],
                <<<END
+----------+-------+--------+---------+---------+
| Project  | Who   | Branch | Status  | Started |
+----------+-------+--------+---------+---------+
| batman:9 | robin | alfred | running |         |
+----------+-------+--------+---------+---------+
END
            ],
        ];
    }
}
