<?php

namespace ByTIC\DeployerRecipies\Tests;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ByticConsoleTest
 * @package ByTIC\DeployerRecipies\Tests
 */
class ByticConsoleTest extends AbstractDepCase
{
    protected function load()
    {
        require DEPLOYER_FIXTURES . '/recipies/bytic-console.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$currentPath = self::$tmpPath . '/localhost';
    }

    public function testCommand()
    {
        $output = $this->start('bytic:command', [], ['verbosity' => OutputInterface::VERBOSITY_DEBUG]);
        self::assertStringContainsString('/bytic namespace:command', $output);
    }
}
