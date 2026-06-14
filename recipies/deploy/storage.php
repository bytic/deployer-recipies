<?php

declare(strict_types=1);

namespace Deployer;

desc('Create storage symlink in current release');
task(
    'deploy:storage-symlink',
    static function (): void {
        run('cd {{deploy_path}} && {{bin/symlink}} {{release_path}}/storage/app/public current/public/uploads');
    }
);
