<?php

namespace Meruhook\MeruhookSDK\Requests\Billing;

use Meruhook\MeruhookSDK\DTOs\BillingBreakdown;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetBillingBreakdownRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/billing/breakdown';
    }

    public function createDtoFromResponse(Response $response): BillingBreakdown
    {
        return BillingBreakdown::fromArray($response->json('data'));
    }
}
