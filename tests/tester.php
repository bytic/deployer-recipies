<?php

declare(strict_types=1);

namespace Deployer;

use ByTIC\DeployerRecipies\Tests\AbstractDepCase;
use Symfony\Component\Process\Process;

/**
 * Run a shell command, optionally prefixed with a `cd` into the current test path.
 */
function exec(string $command): string
{
    if (!empty(AbstractDepCase::$currentPath)) {
        $command = 'cd ' . AbstractDepCase::$currentPath . ' && ' . $command;
    }

    $process = Process::fromShellCommandline($command);
    $process->mustRun();

    return trim($process->getOutput());
}
