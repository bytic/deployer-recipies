<?php

declare(strict_types=1);

namespace ByTIC\DeployerRecipies\Tests;

use Deployer\Host\Localhost;
use Deployer\Task\Context;

class SlackTest extends AbstractDepCase
{
    protected function load(): void
    {
        require __DIR__ . '/../../common/slack.php';
    }

    public function testNotificationsAreSkippedWithoutAWebhook(): void
    {
        $this->reset();
        $host = new Localhost('test');

        self::assertTrue($this->deployer->tasks->has('slack:notify'));
        $this->deployer->tasks->get('slack:notify')->run(new Context($host));
    }
}
