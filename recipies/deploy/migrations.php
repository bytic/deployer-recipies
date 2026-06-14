<?php

declare(strict_types=1);

namespace Deployer;

desc('Deploy migrations');
task('deploy:migrations', ['bytic:migrations:migrate']);

desc('Run migrations');
task('bytic:migrations:migrate', bytic('migrations:migrate'));

