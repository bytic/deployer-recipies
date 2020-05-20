<?php

/* (c) Gabriel Solomon <hello@gabrielsolomon.ro>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This recipe supports ByTic Framework 0.9
 */

namespace Deployer;

require 'recipe/common.php';
require 'recipe/cloudflare.php';

require_once __DIR__ . '/bytic-config.php';

require_once __DIR__ . '/npm.php';
require_once __DIR__ . '/git-submodules.php';
require_once __DIR__.'/bytic-commands.php';
require_once __DIR__.'/bytic-console.php';


/*** DEFINE TASKS ***/
task('deploy:storage-symlink', function () {
    run("cd {{deploy_path}} && {{bin/symlink}} {{release_path}}/storage/app/public current/public/uploads ");
});

task(
    'deploy:git-cache',
    function () {
        run('git config --global core.compression 0');
    }
);

/*** MAIN TASK ***/
desc('Deploy your project');
task('deploy', [
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
]);

/**
 * Helper tasks
 */
desc('Optimize deployed application');
task('deploy:optimize', ['bytic:optimize']);

before('deploy:update_code', 'deploy:git-cache');
after('deploy:symlink', 'deploy:storage-symlink');
after('deploy:failed', 'deploy:unlock');
