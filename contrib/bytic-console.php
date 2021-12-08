<?php

namespace Deployer;

/**
 * Path to BYTIC
 */

set('bin/bytic', function () {
//    try {
//        $byticPath = run('which bytic');
//    } catch (RuntimeException $e) {
    $byticPath = null;
//    }

    if ($byticPath !== null) {
        return "bytic";
    }
    if (test('[ -f {{release_path}}/vendor/bin/bytic ]')) {
        return "{{release_path}}/vendor/bin/bytic";
    }
    if (test('[ -f ~/.composer/vendor/bin/bytic ]')) {
        return '~/.composer/vendor/bin/bytic';
    }
    throw new \RuntimeException('Cannot find bytic. Please specify path to bytic manually');
});

/**
 * Make Bytic command
 *
 * @param string $cmdName Name of command
 * @param array $conf Command options(config)
 *
 * @return string Phinx command to execute
 */
set('bytic_get_cmd', function () {
    return function ($cmdName, $conf) {
        $bytic = get('bin/bytic');

        $byticCmd = "$bytic $cmdName";

        $options = '';

        foreach ($conf as $name => $value) {
            $options .= " --$name $value";
        }

        $byticCmd .= $options;

        return $byticCmd;
    };
});

/**
 * Run an bytic command.
 * @param string $command The artisan command (with cli options if any).
 * @param array $options The options that define the behaviour of the command.
 * @return callable A function that can be used as a task.
 */
function bytic($command, $options = [])
{
    return function () use ($command, $options) {
//        // Ensure we warn or fail when a command relies on the ".env" file.
//        if (in_array('failIfNoEnv', $options) && !test('[ -s {{release_or_current_path}}/.env ]')) {
//            throw new \Exception('Your .env file is empty! Cannot proceed.');
//        }
//
//        if (in_array('skipIfNoEnv', $options) && !test('[ -s {{release_or_current_path}}/.env ]')) {
//            warning("Your .env file is empty! Skipping...</>");
//            return;
//        }

        $byticCmd = get('bytic_get_cmd')('config:cache', $options);

        // Run the artisan command.
        $output = run("{{bin/php}} $byticCmd");

        // Output the results when appropriate.
//        if (in_array('showOutput', $options)) {
            writeln("<info>$output</info>");
//        }
    };
}