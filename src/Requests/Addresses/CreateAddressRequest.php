<?php

namespace Meruhook\MeruhookSDK\Requests\Addresses;

use Meruhook\MeruhookSDK\DTOs\Address;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateAddressRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $webhookUrl,
        protected bool $isPermanent = true,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/addresses';
    }

    protected function defaultBody(): array
    {
        return [
            'webhook_url' => $this->webhookUrl,
            'is_permanent' => $this->isPermanent,
        ];
    }

    public function createDtoFromResponse(Response $response): Address
    {
        return Address::fromArray($response->json('data'));
    }
}
