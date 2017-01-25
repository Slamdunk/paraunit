<?php

namespace Tests\Unit\Command;

use Paraunit\Command\ParallelCommand;
use Prophecy\Argument;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class ParallelCommandTest
 * @package Tests\Unit\Command
 */
class ParallelCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $phpunitConfig = $this->prophesize('Paraunit\Configuration\PHPUnitConfig');

        $runner = $this->prophesize('Paraunit\Runner\Runner');
        $runner->run(Argument::cetera())->shouldBeCalled()->willReturn(0);

        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->get('paraunit.configuration.phpunit_config')->willReturn($phpunitConfig->reveal());
        $container->get('paraunit.runner.runner')->willReturn($runner->reveal());

        $configuration = $this->prophesize('Paraunit\Configuration\ParallelConfiguration');
        $configuration->buildContainer(Argument::cetera())->shouldBeCalled()->willReturn($container);

        $command = new ParallelCommand($configuration->reveal());
        $application = new Application();
        $application->add($command);
        $command = $application->find('run');
        $commandTester = new CommandTester($command);

        $exitCode = $commandTester->execute(array(
            'command' => $command->getName(),
            'stringFilter' => 'someFilter',
            '--testsuite' => 'testSuiteName',
        ));

        $this->assertEquals(0, $exitCode);
    }
}