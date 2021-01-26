<?php

namespace Deployer;

desc('Optimize Bytic');
task('bytic:optimize', function () {
    cd('{{release_path}}');

    $byticCmd = get('bytic_get_cmd')('config:cache', []);

    run($byticCmd);
});