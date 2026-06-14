<?php

declare(strict_types=1);

namespace Deployer;

use Deployer\Exception\Exception;

/**
 * Path to the bytic binary on the remote host.
 * Resolution order: global `bytic` binary, project vendor, global Composer vendor.
 */
set('bin/bytic', function (): string {
    try {
        return run('which bytic');
    } catch (Exception $e) {
        // not in PATH — fall through to local vendor checks
    }

    if (test('[ -f {{release_path}}/vendor/bin/bytic ]')) {
        return '{{release_path}}/vendor/bin/bytic';
    }
    if (test('[ -f ~/.composer/vendor/bin/bytic ]')) {
        return '~/.composer/vendor/bin/bytic';
    }

    throw new \RuntimeException('Cannot find bytic. Please specify path to bytic manually');
});

/**
 * Build a bytic CLI command string.
 *
 * @param string  $cmdName Command name (e.g. "config:cache")
 * @param array<string, scalar> $conf  Named CLI options (key => value)
 */
function byticGetCmd(string $cmdName, array $conf = []): string
{
    $bytic = get('bin/bytic');
    $cmd = "$bytic $cmdName";

    foreach ($conf as $name => $value) {
        $cmd .= " --$name $value";
    }

    return $cmd;
}

/**
 * Return a Deployer task closure that runs a bytic console command.
 *
 * Supported option flags (passed as values in $options array):
 *   - 'failIfNoEnv'  — throw an exception when the .env file is missing/empty
 *   - 'skipIfNoEnv'  — silently skip the task when the .env file is missing/empty
 *   - 'showOutput'   — print the command output to the console
 *
 * @param string  $command The bytic command name (e.g. "migrations:migrate")
 * @param array   $options Option flags and/or CLI options to pass to byticGetCmd()
 * @return callable A closure suitable for use as a Deployer task body
 */
function bytic(string $command, array $options = []): callable
{
    return function () use ($command, $options): void {
        if (in_array('failIfNoEnv', $options, true) && !test('[ -s {{release_or_current_path}}/.env ]')) {
            throw new \RuntimeException('Your .env file is empty! Cannot proceed.');
        }

        if (in_array('skipIfNoEnv', $options, true) && !test('[ -s {{release_or_current_path}}/.env ]')) {
            warning('Your .env file is empty! Skipping...');
            return;
        }

        $byticCmd = byticGetCmd($command, $options);

        $output = run("{{bin/php}} $byticCmd");

        if (in_array('showOutput', $options, true)) {
            writeln("<info>$output</info>");
        }
    };
}
