<?php

namespace Deployer;

require_once 'contrib/webpack_encore.php';

desc('Installs assets');

task('deploy:assets', [
    'deploy:assets:install',
    'deploy:assets:build',
]);

set('assets/package_manager', function () {
    return get('webpack_encore/package_manager');
});

desc('Runs webpack encore build');
task('deploy:assets:install', function () {
    invoke(get('assets/package_manager') . ':install');
});
task('deploy:assets:build', ['webpack_encore:build']);
