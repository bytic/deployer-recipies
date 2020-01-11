<?php

<<<<<<< HEAD
/* (c) Gabriel Solomon <hello@gabrielsolomon.ro>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
=======
/*
>>>>>>> 4.0
 * This recipe supports ByTic Framework 0.9
 */

namespace Deployer;

<<<<<<< HEAD
require 'recipe/common.php';
require 'recipe/npm.php';
require 'recipe/cloudflare.php';

require_once __DIR__ . '/git-submodules.php';

/*** CONFIGURATION ***/
set('ssh_type', 'native');
set('ssh_multiplexing', true);
//set('git_cache', true);
=======
require 'vendor/deployer/deployer/recipe/common.php';
require 'vendor/deployer/recipes/npm.php';
require_once __DIR__.'/git-submodules.php';

/*** CONFIGURATION ***/
//set('ssh_type', 'native');
//set('ssh_multiplexing', true);
set('git_cache', true);
>>>>>>> 4.0

set('keep_releases', 3);
//set('composer_command', 'composer'); // Path to composer
set('writable_use_sudo', false); // Using sudo in writable commands?

//env('composer_options',
//    'install --no-dev --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction');
set('release_name', date('YmdHis')); // name of folder in releases


/*** SHARED FILES ***/
set('shared_files', [
    '.env',
]);

/*** SHARED DIRS ***/
set('shared_dirs', [
    'storage/app',
    'storage/logs',
]);

/*** WRITABLES DIRS ***/
set('writable_dirs', [
    'storage/app',
    'storage/logs',
    'storage/cache',
    'storage/cache/autoloader',
]);

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
    'npm:install',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success',
]);

before('deploy:update_code', 'deploy:git-cache');
after('deploy:symlink', 'deploy:storage-symlink');
