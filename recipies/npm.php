<?php

namespace Deployer;

require 'recipe/npm.php';

set('npm_action', 'ci');
set('npm_options', '');


task('npm:install', function () {
    run("cd {{release_path}} && {{bin/npm}} {{npm_action}}");
});

task('assets:install', ['npm:install']);

task('assets:build', function () {
})->desc('Assets build');
