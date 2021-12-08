<?php

namespace Deployer;

task(
    'deploy:storage-symlink',
    function () {
        run("cd {{deploy_path}} && {{bin/symlink}} {{release_path}}/storage/app/public current/public/uploads ");
    }
);

desc('Optimize Bytic');
task('bytic:optimize', bytic('config:cache'));

task(
    'bytic:previous_release:cleanup_vendors',
    function () {
        if (!has('previous_release')) {
            return;
        }
        if (!test('[ -d {{previous_release}}/vendor ]')) {
            return;
        }
        $sudo = get('cleanup_use_sudo') ? 'sudo' : '';
        run("$sudo rm -rf {{previous_release}}/vendor");
    }
);

task(
    'bytic:previous_release:cleanup_cache',
    function () {
        if (!has('previous_release')) {
            return;
        }
        if (!test('[ -d {{previous_release}}/vendor ]')) {
            return;
        }
//        $sudo = get('cleanup_use_sudo') ? 'sudo' : '';
//        run("$sudo rm -rf {{previous_release}}/vendor");
    }
);

