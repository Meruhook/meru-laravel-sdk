<?php

namespace Meruhook\MeruhookSDK\Requests\Addresses;

use Meruhook\MeruhookSDK\DTOs\Address;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class ListAddressesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/addresses';
    }

    public function createDtoFromResponse(Response $response): array
    {
        $data = $response->json('data');

        return array_map(
            fn (array $address) => Address::fromArray($address),
            $data
        );
    }
}
