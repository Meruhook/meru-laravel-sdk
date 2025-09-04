<?php

namespace Meruhook\MeruhookSDK\Requests\Addresses;

use Meruhook\MeruhookSDK\DTOs\Address;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class UpdateAddressRequest extends Request implements HasBody
{
    use HasJsonBody;
    
    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $addressId,
        protected ?string $webhookUrl = null,
        protected ?bool $isEnabled = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/addresses/{$this->addressId}";
    }

    protected function defaultBody(): array
    {
        $body = [];

        if ($this->webhookUrl !== null) {
            $body['webhook_url'] = $this->webhookUrl;
        }

        if ($this->isEnabled !== null) {
            $body['is_enabled'] = $this->isEnabled;
        }

        return $body;
    }

    public function createDtoFromResponse(Response $response): Address
    {
        return Address::fromArray($response->json('data'));
    }
}
