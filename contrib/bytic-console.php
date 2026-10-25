<?php

declare(strict_types=1);

namespace Deployer;

use Deployer\Exception\Exception;

/*
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
 * @param array<array-key, scalar> $conf Named CLI options (key => value)
 */
function byticGetCmd(string $cmdName, array $conf = []): string
{
    $binary = get('bin/bytic');
    if (!is_string($binary) && !is_int($binary)) {
        throw new \UnexpectedValueException('The configured bytic binary path must be a string or integer.');
    }
    $cmd = quote($binary) . ' ' . quote($cmdName);

    foreach ($conf as $name => $value) {
        if (!is_string($name) || !preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/D', $name)) {
            throw new \InvalidArgumentException('Bytic option names must be valid long-option names.');
        }

        $value = is_bool($value) ? ($value ? 'true' : 'false') : (string) $value;
        $cmd .= ' --' . quote($name) . ' ' . quote($value);
    }

    return $cmd;
}

/**
 * Remove behavior flags from the named CLI arguments.
 *
 * @internal
 * @param array<array-key, scalar> $options
 * @return array<array-key, scalar>
 */
function byticGetCommandOptions(array $options): array
{
    $controlFlags = ['failIfNoEnv', 'skipIfNoEnv', 'showOutput'];
    $commandOptions = [];

    foreach ($options as $key => $value) {
        if (
            (is_int($key) && in_array($value, $controlFlags, true))
            || (is_string($key) && in_array($key, $controlFlags, true))
        ) {
            continue;
        }
        $commandOptions[$key] = $value;
    }

    return $commandOptions;
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
 * @param array<array-key, scalar> $options Control flags and/or CLI options to pass to byticGetCmd()
 * @return callable A closure suitable for use as a Deployer task body
 */
function bytic(string $command, array $options = []): callable
{
    return function () use ($command, $options): void {
        $controlFlags = ['failIfNoEnv', 'skipIfNoEnv', 'showOutput'];
        $enabledFlags = [];
        foreach ($controlFlags as $flag) {
            $enabledFlags[$flag] = in_array($flag, $options, true) || ($options[$flag] ?? false) === true;
        }

        if ($enabledFlags['failIfNoEnv'] && !test('[ -s {{release_or_current_path}}/.env ]')) {
            throw new \RuntimeException('Your .env file is empty! Cannot proceed.');
        }

        if ($enabledFlags['skipIfNoEnv'] && !test('[ -s {{release_or_current_path}}/.env ]')) {
            warning('Your .env file is empty! Skipping...');
            return;
        }

        $commandOptions = byticGetCommandOptions($options);
        $byticCmd = byticGetCmd($command, $commandOptions);

        $output = run("{{bin/php}} $byticCmd");

        if ($enabledFlags['showOutput']) {
            writeln("<info>$output</info>");
        }
    };
}
