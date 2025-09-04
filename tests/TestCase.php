<?php

namespace Meruhook\MeruhookSDK\Tests;

use Meruhook\MeruhookSDK\MeruServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            MeruServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('meru.api_token', 'test-token');
        config()->set('meru.base_url', 'https://api.test.com');
        config()->set('meru.webhook.secret', 'test-secret');
    }
}
