<?php

/*
 * This recipe supports ByTic Framework 0.9
 */

namespace Deployer;

require 'vendor/deployer/deployer/recipe/common.php';

require_once __DIR__.'/npm.php';
require_once __DIR__.'/git-submodules.php';
require_once __DIR__.'/bytic-commands.php';
require_once __DIR__.'/bytic-console.php';

/*** CONFIGURATION ***/
//set('ssh_type', 'native');
//set('ssh_multiplexing', true);
set('git_cache', true);


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
task('artisan:up', ['bytic:optimize']);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
