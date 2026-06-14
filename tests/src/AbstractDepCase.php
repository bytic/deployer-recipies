<?php

declare(strict_types=1);

namespace ByTIC\DeployerRecipies\Tests;

use Deployer\Deployer;
use Deployer\Task\Context;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;

abstract class AbstractDepCase extends BaseTestCase
{
    private ApplicationTester $tester;

    protected Deployer $deployer;

    public static string $tmpPath = '';

    public static string $currentPath = '';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Prepare FS
        self::$tmpPath = DEPLOYER_FIXTURES . '/tmp';
        self::cleanUp();

        mkdir(self::$tmpPath);
        self::$tmpPath = (string) realpath(self::$tmpPath);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::cleanUp();
    }

    protected static function cleanUp(): void
    {
        if (is_dir(self::$tmpPath)) {
            \exec('rm -rf ' . self::$tmpPath);
        }
    }

    public function reset(): void
    {
        // Create app tester
        $console = new Application();
        $console->setAutoExit(false);
        $console->setCatchExceptions(false);
        $this->tester = new ApplicationTester($console);

        // Prepare Deployer
        $input  = $this->createMock(Input::class);
        $output = $this->createMock(Output::class);

        $this->deployer = new Deployer($console, $input, $output);

        // Clear context
        Context::pop();

        // Load recipe
        $this->load();

        // Init Deployer
        $this->deployer->init();
    }

    /**
     * Load the recipe under test.
     */
    abstract protected function load(): void;

    /**
     * Execute a Deployer command with the application tester.
     *
     * @param string  $command
     * @param array<string, mixed> $args
     * @param array<string, mixed> $options
     */
    protected function start(string $command, array $args = [], array $options = []): string
    {
        $this->reset();
        $this->tester->run(['command' => $command] + $args, $options);
        clearstatcache(true, self::$tmpPath);

        return $this->tester->getDisplay();
    }
}
