<?php

namespace Deployer;

set('bin/npm', function () {
    return run('which npm');
});

set('npm_action', 'ci');
set('npm_options', '');

desc('Install npm packages');
//task('npm:install', function () {
//    if (has('previous_release')) {
//        if (test('[ -d {{previous_release}}/node_modules ]')) {
//            run('cp -R {{previous_release}}/node_modules {{release_path}}');
//        }
//    }
//    run("cd {{release_path}} && {{bin/npm}} install");
//});

task('npm:install', function () {
    run("cd {{release_path}} && {{bin/npm}} {{npm_action}}");
});

task('assets:install', ['npm:install']);

task('assets:build', function () {
})->desc('Assets build');
