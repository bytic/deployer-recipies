<?php

namespace Deployer;

require_once __DIR__ . '/../../contrib/bytic-commands.php';

desc('Optimize deployed application');
task('deploy:optimize', ['bytic:optimize']);
