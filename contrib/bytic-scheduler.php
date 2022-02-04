<?php

declare(strict_types=1);

namespace Deployer;

use Bytic\Scheduler\Console\PublishCommand;

/*** SCHEDULER PUBLISH***/
desc('Publish schedule events');
task('bytic:scheduler:publish', bytic(PublishCommand::NAME));
