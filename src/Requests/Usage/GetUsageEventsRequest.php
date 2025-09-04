<?php

namespace Meruhook\MeruhookSDK\Requests\Usage;

use Meruhook\MeruhookSDK\DTOs\UsageEvent;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUsageEventsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected int $limit = 50,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/usage/events';
    }

    protected function defaultQuery(): array
    {
        return [
            'limit' => $this->limit,
        ];
    }

    public function createDtoFromResponse(Response $response): array
    {
        $data = $response->json('data');

        return array_map(
            fn (array $event) => UsageEvent::fromArray($event),
            $data
        );
    }
}
