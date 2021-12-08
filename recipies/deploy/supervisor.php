<?php

declare(strict_types=1);

namespace Deployer;

require __DIR__ . '/../../common/supervisor.php';

before('supervisor:upload', 'supervisor:stop');
before('deploy:symlink', 'supervisor:upload');

after('deploy:success', 'supervisor:start');
after('deploy:failed', 'supervisor:start');
after('rollback', 'supervisor:start');
