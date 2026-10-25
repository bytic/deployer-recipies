<?php

declare(strict_types=1);

namespace ByTIC\DeployerRecipies\Tests;

use Deployer\Host\Localhost;
use Deployer\Task\Context;

class CloudflareTest extends AbstractDepCase
{
    protected function load(): void
    {
        require __DIR__ . '/../../common/cloudflare.php';
    }

    public function testMissingCredentialsFailBeforeMakingARequest(): void
    {
        $this->reset();
        $host = new Localhost('test');
        $this->deployer->config->set('cloudflare', []);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cloudflare: provide a service_key, api_token, or email + api_key.');

        $this->deployer->tasks->get('deploy:cloudflare')->run(new Context($host));
    }
}
