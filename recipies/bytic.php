<?php

/*
 * This recipe supports ByTic Framework 0.9
 */

namespace Deployer;

require 'vendor/deployer/deployer/recipe/common.php';

require_once __DIR__ . '/bytic-config.php';

require_once __DIR__.'/npm.php';
require_once __DIR__.'/git-submodules.php';
require_once __DIR__.'/bytic-commands.php';
require_once __DIR__.'/bytic-console.php';


/*** MAIN TASK ***/
desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
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
    'current',
    'success',
]);

/**
 * Helper tasks
 */
desc('Optimize deployed application');
task('deploy:optimize', ['bytic:optimize']);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
