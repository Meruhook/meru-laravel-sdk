<?php

namespace Meruhook\MeruhookSDK\Requests\Addresses;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class DeleteAddressRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $addressId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/addresses/{$this->addressId}";
    }
}