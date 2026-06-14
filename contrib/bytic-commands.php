<?php

declare(strict_types=1);

namespace Deployer;

desc('Optimize Bytic');
task('bytic:optimize', bytic('config:cache'));

desc('Remove vendor directory from previous release');
task(
    'bytic:previous_release:cleanup_vendors',
    static function (): void {
        if (!has('previous_release')) {
            return;
        }
        if (!test('[ -d {{previous_release}}/vendor ]')) {
            return;
        }
        $sudo = get('cleanup_use_sudo') ? 'sudo ' : '';
        run("{$sudo}rm -rf {{previous_release}}/vendor");
    }
);

desc('Remove cache directory from previous release');
task(
    'bytic:previous_release:cleanup_cache',
    static function (): void {
        if (!has('previous_release')) {
            return;
        }
        if (!test('[ -d {{previous_release}}/bootstrap/cache ]')) {
            return;
        }
        $sudo = get('cleanup_use_sudo') ? 'sudo ' : '';
        run("{$sudo}rm -rf {{previous_release}}/bootstrap/cache");
    }
);

