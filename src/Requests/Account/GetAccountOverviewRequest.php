<?php

namespace Meruhook\MeruhookSDK\Requests\Account;

use Meruhook\MeruhookSDK\DTOs\AccountOverview;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAccountOverviewRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/account';
    }

    public function createDtoFromResponse(Response $response): AccountOverview
    {
        return AccountOverview::fromArray($response->json('data'));
    }
}