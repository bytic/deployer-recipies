<?php

declare(strict_types=1);

namespace ByTIC\DeployerRecipies\Tests;

use Symfony\Component\Console\Output\OutputInterface;

class ByticConsoleTest extends AbstractDepCase
{
    protected function load(): void
    {
        require DEPLOYER_FIXTURES . '/recipies/bytic-console.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$currentPath = self::$tmpPath . '/localhost';
    }

    public function testCommand(): void
    {
        $output = $this->start('bytic:command', [], ['verbosity' => OutputInterface::VERBOSITY_DEBUG]);
        self::assertStringContainsString('/bytic namespace:command', $output);
    }
}
