<?php

namespace Meruhook\MeruhookSDK\Exceptions;

use Exception;
use Saloon\Http\Response;

class MeruException extends Exception
{
    public function __construct(
        string $message,
        protected ?Response $response = null,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public static function fromResponse(Response $response): static
    {
        $data = $response->json();
        $message = $data['message'] ?? 'An error occurred';

        return new static($message, $response, $response->status());
    }
}