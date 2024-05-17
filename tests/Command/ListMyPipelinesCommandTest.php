<?php

namespace Ringli\Test\Command;

use Ringli\Command\ListMyPipelinesCommand;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Responsible for testing \Ringli\Command\ListMyPipelinesCommand
 */
class ListMyPipelinesCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Ringli\Command\ListMyPipelinesCommand::__construct
     * @covers \Ringli\Command\ListMyPipelinesCommand::configure
     */
    public function testConfigure(): void
    {
        $client = $this->createMock('\Ringli\Client');

        $command = new ListMyPipelinesCommand($client);

        $this->assertEquals($command->getName(), 'mine');
        $this->assertEquals(
            $command->getDescription(),
            'List my pipelines'
        );
    }

    /**
     * @covers \Ringli\Command\ListMyPipelinesCommand::execute
     * @covers \Ringli\Command\ListMyPipelinesCommand::decorateRowByStatus
     * @dataProvider provideDataForExecute
     *
     * @param array<mixed> $pipeline
     * @param array<mixed> $workflows
     * @param string $expected
     */
    public function testExecuteCanRenderWhatWeWant(array $pipeline, array $workflows, string $expected): void
    {
        $client = $this->createMock('\Ringli\Client');
        $client
            ->method('getMyPipelines')
            ->willReturn(['items' => [$pipeline]]);

        $client
            ->method('getWorkflowForPipeline')
            ->willReturn(['items' => $workflows]);

        $client
            ->method('getOrg')
            ->willReturn("Gotham");

        $tester = new CommandTester(new ListMyPipelinesCommand($client));
        $tester->execute(["repo" => "batman"]);

        $this->assertEquals($expected, trim($tester->getDisplay()));
    }

    /**
     * Data provider for testExecuteCanRenderWhatWeWant
     *
     * @return array<mixed>
     */
    public static function provideDataForExecute(): array
    {
        return [
            [
                ['id' => 1, 'project_slug' => 'batman'],
                [],
                <<<END
Report for Gotham
+---------+-----+--------+--------+---------+
| Project | Who | Branch | Status | Started |
+---------+-----+--------+--------+---------+
END
            ],
            [
                ['id' => 1, 'project_slug' => 'batman'],
                [[]],
                <<<END
Report for Gotham
+---------+-----+--------+--------+---------+
| Project | Who | Branch | Status | Started |
+---------+-----+--------+--------+---------+
END
            ],
            [
                ['id' => 1, 'project_slug' => 'batman'],
                [['pipeline_number' => 1, 'status' => 'running']],
                <<<END
Report for Gotham
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
                [['pipeline_number' => 9, 'status' => 'running']],
                <<<END
Report for Gotham
+----------+-------+--------+---------+---------+
| Project  | Who   | Branch | Status  | Started |
+----------+-------+--------+---------+---------+
| batman:9 | robin | alfred | running |         |
+----------+-------+--------+---------+---------+
END
            ],
            [
                [
                    'id' => 5, 'project_slug' => 'batman', 'trigger' =>
                    ['actor' => ['login' => 'robin']], 'vcs' => ['branch' => 'alfred']
                ],
                [['pipeline_number' => 9, 'status' => 'success']],
                <<<END
Report for Gotham
+----------+-------+--------+---------+---------+
| Project  | Who   | Branch | Status  | Started |
+----------+-------+--------+---------+---------+
| batman:9 | robin | alfred | success |         |
+----------+-------+--------+---------+---------+
END
            ],
            [
                [
                    'id' => 5, 'project_slug' => 'batman', 'trigger' =>
                    ['actor' => ['login' => 'robin']], 'vcs' => ['branch' => 'alfred']
                ],
                [['pipeline_number' => 9, 'status' => 'failed']],
                <<<END
Report for Gotham
+----------+-------+--------+--------+---------+
| Project  | Who   | Branch | Status | Started |
+----------+-------+--------+--------+---------+
| batman:9 | robin | alfred | failed |         |
+----------+-------+--------+--------+---------+
END
            ],
            [
                [
                    'id' => 5, 'project_slug' => 'batman', 'trigger' =>
                    ['actor' => ['login' => 'robin']], 'vcs' => ['branch' => 'alfred']
                ],
                [['pipeline_number' => 9, 'status' => 'success']],
                <<<END
Report for Gotham
+----------+-------+--------+---------+---------+
| Project  | Who   | Branch | Status  | Started |
+----------+-------+--------+---------+---------+
| batman:9 | robin | alfred | success |         |
+----------+-------+--------+---------+---------+
END
            ],
            [
                [
                    'id' => 5, 'project_slug' => 'batman', 'trigger' =>
                    ['actor' => ['login' => 'robin']], 'vcs' => ['branch' => 'alfred']
                ],
                [['pipeline_number' => 9, 'status' => '']],
                <<<END
Report for Gotham
+----------+-------+--------+--------+---------+
| Project  | Who   | Branch | Status | Started |
+----------+-------+--------+--------+---------+
| batman:9 | robin | alfred |        |         |
+----------+-------+--------+--------+---------+
END
            ],
        ];
    }
}
