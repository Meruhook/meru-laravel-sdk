<?php

namespace Meruhook\MeruhookSDK\Requests\Billing;

use Meruhook\MeruhookSDK\DTOs\Billing;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetBillingRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/billing';
    }

    public function createDtoFromResponse(Response $response): Billing
    {
        return Billing::fromArray($response->json('data'));
    }
}
