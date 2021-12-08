<?php

namespace Deployer;

require 'recipe/common.php';

require_once __DIR__ . '/../contrib/bytic-console.php';

require __DIR__ . '/../common/cloudflare.php';

require __DIR__ . '/deploy/assets.php';
require __DIR__ . '/deploy/cleanup.php';
require __DIR__ . '/deploy/config.php';
require __DIR__ . '/deploy/migrations.php';
require __DIR__ . '/deploy/optimize.php';
require __DIR__ . '/deploy/scheduler.php';
require __DIR__ . '/deploy/supervisor.php';

/*** MAIN TASK ***/
desc('Deploy your project');
task(
    'deploy',
    [
        'deploy:prepare',
        'deploy:vendors',
        'deploy:assets',
        'deploy:optimize',
        'deploy:publish',
        'deploy:migrations',
    ]
);

/**
 * Helper tasks
 */
after('deploy:symlink', 'deploy:storage-symlink');
after('deploy:failed', 'deploy:unlock');
