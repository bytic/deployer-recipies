<?php

namespace Deployer;

set('bin/yarn', function () {
    return run('which yarn');
});

desc('Install Yarn packages');
task('yarn:install', function () {
    if (has('previous_release')) {
        if (test('[ -d {{previous_release}}/node_modules ]')) {
            run('cp -R {{previous_release}}/node_modules {{release_path}}');
        }
    }
    run("cd {{release_path}} && {{bin/yarn}}");
});

task('assets:install', ['yarn:install']);

task('assets:build', function () {
})->desc('Assets build');
