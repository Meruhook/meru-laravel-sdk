<?php

namespace Meruhook\MeruhookSDK\Requests\Usage;

use Meruhook\MeruhookSDK\DTOs\Usage;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUsagePeriodRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $period, // YYYY-MM format
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/usage/{$this->period}";
    }

    public function createDtoFromResponse(Response $response): Usage
    {
        return Usage::fromArray($response->json('data'));
    }
}
