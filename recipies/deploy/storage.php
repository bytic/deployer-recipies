<?php

declare(strict_types=1);

namespace Deployer;

desc('Create storage symlink in current release');
task(
    'deploy:storage-symlink',
    static function (): void {
        run(
            'cd {{deploy_path}} && {{bin/symlink}} ' .
            quote('{{release_path}}/storage/app/public') . ' ' .
            quote('current/public/uploads')
        );
    }
);
