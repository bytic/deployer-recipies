<?php

declare(strict_types=1);

namespace Deployer;

desc('Optimize deployed application');
task('deploy:optimize', ['bytic:optimize']);
