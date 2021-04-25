<?php

namespace Deployer;

require 'recipe/common.php';
require __DIR__ . '/../common/cloudflare.php';
require __DIR__ . '/../common/node_modules.php';
require __DIR__ . '/../common/npm.php';

require_once __DIR__ . '/bytic-config.php';
require_once __DIR__ . '/git-submodules.php';
require_once __DIR__ . '/bytic-commands.php';
require_once __DIR__ . '/bytic-console.php';

/*** DEFINE TASKS ***/
task(
    'deploy:git-cache',
    function () {
        run('git config --global core.compression 0');
    }
);

/*** MAIN TASK ***/
desc('Deploy your project');
task(
    'deploy',
    [
        'deploy:prepare',
        'deploy:lock',
        'deploy:release',
        'deploy:update_code',
        'deploy:shared',
        'deploy:writable',
        'deploy:vendors',
        'assets:install',
        'assets:build',
        'deploy:clear_paths',
        'deploy:optimize',
        'deploy:symlink',
        'deploy:unlock',
        'cleanup',
        'success',
    ]
);

/**
 * Helper tasks
 */
before('deploy:update_code', 'deploy:git-cache');
after('deploy:symlink', 'deploy:storage-symlink');
after('deploy:failed', 'deploy:unlock');

desc('Optimize deployed application');
task('deploy:optimize', ['bytic:optimize']);

after('cleanup', 'cleanup:current_release');
after('cleanup', 'cleanup:previous_release');

task(
    'cleanup:current_release',
    ['deploy:cleanup:node']
);

task(
    'cleanup:previous_release',
    [
        'deploy:previous_release:cleanup_node',
        'bytic:previous_release:cleanup_vendors',
        'bytic:previous_release:cleanup_cache'
    ]
);
