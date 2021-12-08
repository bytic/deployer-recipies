<?php

namespace Deployer;

require __DIR__ . '/../../common/node_modules.php';

after('deploy:cleanup', 'cleanup:current_release');
after('deploy:cleanup', 'cleanup:previous_release');

task(
    'cleanup:current_release',
    ['deploy:cleanup:node']
);

task(
    'cleanup:previous_release',
    [
        'deploy:previous_release:cleanup_node',
        'bytic:previous_release:cleanup_vendors',
        'bytic:previous_release:cleanup_cache'
    ]
);
