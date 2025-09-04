<?php

namespace Meruhook\MeruhookSDK\Resources;

use Meruhook\MeruhookSDK\DTOs\AccountOverview;
use Meruhook\MeruhookSDK\DTOs\ApiToken;
use Meruhook\MeruhookSDK\DTOs\User;
use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Requests\Account\GetAccountOverviewRequest;
use Meruhook\MeruhookSDK\Requests\Auth\CreateApiTokenRequest;
use Meruhook\MeruhookSDK\Requests\Auth\GetUserRequest;

class AccountResource
{
    public function __construct(
        protected MeruConnector $connector,
    ) {}

    /**
     * Get authenticated user information
     */
    public function user(): User
    {
        $request = new GetUserRequest;
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Get combined account overview (addresses, usage, billing)
     */
    public function overview(): AccountOverview
    {
        $request = new GetAccountOverviewRequest;
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Create a new API token
     */
    public function createApiToken(string $name): ApiToken
    {
        $request = new CreateApiTokenRequest($name);
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }
}
