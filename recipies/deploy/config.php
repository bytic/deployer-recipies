<?php

namespace Deployer;

/*** CONFIGURATION ***/
//set('ssh_type', 'native');
//set('ssh_multiplexing', true);
set('git_cache', true);

set('keep_releases', 3);
//set('composer_command', 'composer'); // Path to composer

set('writable_use_sudo', false); // Using sudo in writable commands?

// Use recursive mode (-R)?
set('writable_recursive', true);

// The chmod mode.
set('writable_chmod_mode', '0775');

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
    'bootstrap/cache',
    'bootstrap/cache/routes',
    'storage/app',
    'storage/logs',
    'storage/cache',
    'storage/cache/autoloader',
]);
