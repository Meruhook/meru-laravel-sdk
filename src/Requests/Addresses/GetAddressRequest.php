<?php

namespace Meruhook\MeruhookSDK\Requests\Addresses;

use Meruhook\MeruhookSDK\DTOs\Address;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAddressRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $addressId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/addresses/{$this->addressId}";
    }

    public function createDtoFromResponse(Response $response): Address
    {
        return Address::fromArray($response->json('data'));
    }
}