<?php

namespace Meruhook\MeruhookSDK\Requests\Usage;

use Meruhook\MeruhookSDK\DTOs\Usage;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUsageRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/usage';
    }

    public function createDtoFromResponse(Response $response): Usage
    {
        return Usage::fromArray($response->json('data'));
    }
}
