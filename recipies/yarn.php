<?php

namespace Deployer;

require 'recipe/yarn.php';

task('assets:install', ['yarn:install']);

task('assets:build', function () {
})->desc('Assets build');
