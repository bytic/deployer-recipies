<?php

namespace Deployer;

require BYTIC_RECIPIES . 'bytic-console.php';

set('release_path', dirname(dirname(dirname(__DIR__))));
set('bin/bytic', '{{release_path}}/vendor/bin/bytic');

task('bytic:command', function () {
    $byticCmd = get('bytic_get_cmd')('namespace:command', []);

    writeln($byticCmd);
});
