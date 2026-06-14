<?php

declare(strict_types=1);

namespace Deployer;

/*** CONFIGURATION ***/
set('git_cache', true);
set('keep_releases', 3);
set('writable_use_sudo', false);
set('writable_recursive', true);
set('writable_chmod_mode', '0775');
set('release_name', date('YmdHis'));

/*** SHARED FILES ***/
set('shared_files', [
    '.env',
]);

/*** SHARED DIRS ***/
set('shared_dirs', [
    'storage/app',
    'storage/logs',
]);

/*** WRITABLE DIRS ***/
set('writable_dirs', [
    'bootstrap/cache',
    'bootstrap/cache/routes',
    'storage/app',
    'storage/logs',
    'storage/cache',
    'storage/cache/autoloader',
]);
