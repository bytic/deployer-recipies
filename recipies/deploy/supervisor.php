<?php

declare(strict_types=1);

namespace Deployer;

require __DIR__ . '/../../common/supervisor.php';

before('deploy:symlink', 'supervisor:upload');

after('deploy:success', 'supervisor:start');
after('deploy:failed', 'supervisor:start');
after('rollback', 'supervisor:start');
