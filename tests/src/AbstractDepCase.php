<?php

declare(strict_types=1);

namespace ByTIC\DeployerRecipies\Tests;

use Deployer\Deployer;
use Deployer\Task\Context;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Console\Application;

abstract class AbstractDepCase extends BaseTestCase
{
    protected Deployer $deployer;

    public function reset(): void
    {
        $console = new Application();
        $console->setAutoExit(false);
        $console->setCatchExceptions(false);

        Context::pop();
        Deployer::resetInstance();
        $this->deployer = new Deployer($console);

        // Load recipe
        $this->load();

        // Init Deployer
        $this->deployer->init();
    }

    /**
     * Load the recipe under test.
     */
    abstract protected function load(): void;
}
