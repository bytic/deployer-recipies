<?php

namespace Deployer;

use ByTIC\DeployerRecipies\Tests\AbstractDepCase;
use Deployer\Console\Application;
use Deployer\Task\Context;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Process\Process;

/**
 * @param $command
 * @return string
 */
function exec($command)
{
    if (!empty(AbstractDepCase::$currentPath)) {
        $command = 'cd '.AbstractDepCase::$currentPath.' && '.$command;
    }
    if (method_exists('Symfony\Component\Process\Process', 'fromShellCommandline')) {
        $process = Process::fromShellCommandline($command);
    } else {
        $process = new Process($command);
    }
    $process
        ->mustRun();

    return trim($process->getOutput());
}
