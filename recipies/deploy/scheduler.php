<?php

declare(strict_types=1);

namespace Deployer;

require_once __DIR__ . '/../../contrib/bytic-scheduler.php';

after('deploy:cleanup', 'bytic:scheduler:publish');
