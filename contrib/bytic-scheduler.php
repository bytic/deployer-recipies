<?php

namespace Deployer;

/*** SCHEDULER PUBLISH***/
desc('Publish schedule events');
task('bytic:scheduler:publish', bytic('schedule:register'));
