<?php

namespace Meruhook\MeruhookSDK\Auth;

use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class BearerTokenAuth implements Authenticator
{
    public function __construct(
        protected string $token,
    ) {}

    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', 'Bearer '.$this->token);
    }
}
