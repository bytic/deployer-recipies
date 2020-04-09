<?php

namespace Deployer;

require 'vendor/deployer/recipes/yarn.php';

task('assets:install', ['yarn:install']);

task('assets:build', function () {
})->desc('Assets build');
