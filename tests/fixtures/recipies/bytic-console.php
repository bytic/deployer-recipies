<?php

declare(strict_types=1);

namespace Deployer;

require BYTIC_CONTRIB . 'bytic-console.php';

set('release_path', dirname(dirname(dirname(__DIR__))));
set('bin/bytic', '{{release_path}}/vendor/bin/bytic');

task('bytic:command', static function (): void {
    $byticCmd = byticGetCmd('namespace:command');

    writeln($byticCmd);
});
