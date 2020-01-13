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
 * Make Phinx command
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
