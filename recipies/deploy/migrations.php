<?php

namespace Deployer;

require __DIR__ . '/../../common/phinx.php';

desc('Deploy migrations');
task('deploy:migrations', ['bytic:migrations:migrate']);

desc('Run migrations');
task('bytic:migrations:migrate', bytic('migrations:migrate'));

