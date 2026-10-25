<?php

declare(strict_types=1);

namespace ByTIC\DeployerRecipies\Tests;

class ByticConsoleTest extends AbstractDepCase
{
    protected function load(): void
    {
        require DEPLOYER_FIXTURES . '/recipies/bytic-console.php';
    }

    public function testCommand(): void
    {
        $this->reset();
        $command = \Deployer\byticGetCmd('namespace:command');

        self::assertStringContainsString('/vendor/bin/bytic', $command);
        self::assertStringContainsString('namespace:command', $command);
    }

    public function testCommandArgumentsAreQuoted(): void
    {
        $this->reset();
        $command = \Deployer\byticGetCmd('namespace:command', ['message' => 'hello world']);

        self::assertStringContainsString("--message " . \Deployer\quote('hello world'), $command);
    }

    public function testControlFlagsAreNotPassedToTheCli(): void
    {
        self::assertSame(
            ['message' => 'hello world'],
            \Deployer\byticGetCommandOptions([
                'showOutput',
                'failIfNoEnv' => true,
                'message' => 'hello world',
            ])
        );
    }
}
