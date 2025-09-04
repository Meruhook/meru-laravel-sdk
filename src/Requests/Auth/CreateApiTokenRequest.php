<?php

namespace Meruhook\MeruhookSDK\Requests\Auth;

use Meruhook\MeruhookSDK\DTOs\ApiToken;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class CreateApiTokenRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        protected string $name,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api-keys';
    }

    protected function defaultBody(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public function createDtoFromResponse(Response $response): ApiToken
    {
        return ApiToken::fromArray($response->json('data'));
    }
}
