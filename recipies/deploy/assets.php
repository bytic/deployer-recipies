<?php

declare(strict_types=1);

namespace Deployer;

require_once 'contrib/webpack_encore.php';

/**
 * Package manager used to install frontend dependencies.
 * Defaults to the webpack_encore recipe's own setting when available,
 * falling back to 'npm' if the webpack_encore config key is not set.
 *
 * Override in your deploy.php:
 *   set('assets/package_manager', 'yarn');
 */
set('assets/package_manager', static function (): string {
    if (has('webpack_encore/package_manager')) {
        return get('webpack_encore/package_manager');
    }
    return 'npm';
});

desc('Install and build frontend assets');
task('deploy:assets', [
    'deploy:assets:install',
    'deploy:assets:build',
]);

desc('Install frontend dependencies');
task('deploy:assets:install', static function (): void {
    invoke(get('assets/package_manager') . ':install');
});

desc('Build frontend assets with Webpack Encore');
task('deploy:assets:build', ['webpack_encore:build']);
