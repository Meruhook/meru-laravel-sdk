<?php

namespace Meruhook\MeruhookSDK;

use Meruhook\MeruhookSDK\Auth\BearerTokenAuth;
use Meruhook\MeruhookSDK\Resources\AccountResource;
use Meruhook\MeruhookSDK\Resources\AddressResource;
use Meruhook\MeruhookSDK\Resources\BillingResource;
use Meruhook\MeruhookSDK\Resources\UsageResource;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;

class MeruConnector extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors, HasTimeout;

    public function __construct(
        protected string $apiToken,
        protected string $baseUrl = 'https://api.meruhook.com'
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new BearerTokenAuth($this->apiToken);
    }

    protected function defaultConfig(): array
    {
        return [
            'timeout' => config('meru.timeout', 30),
        ];
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'MeruSDK/1.0',
        ];
    }

    public function addresses(): AddressResource
    {
        return new AddressResource($this);
    }

    public function usage(): UsageResource
    {
        return new UsageResource($this);
    }

    public function billing(): BillingResource
    {
        return new BillingResource($this);
    }

    public function account(): AccountResource
    {
        return new AccountResource($this);
    }
}
