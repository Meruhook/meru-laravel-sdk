<?php

namespace Meruhook\MeruhookSDK\Requests\Auth;

use Meruhook\MeruhookSDK\DTOs\User;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUserRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/user';
    }

    public function createDtoFromResponse(Response $response): User
    {
        return User::fromArray($response->json('data'));
    }
}
