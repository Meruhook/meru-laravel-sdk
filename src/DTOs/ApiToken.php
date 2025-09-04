<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class ApiToken
{
    public function __construct(
        public string $id,
        public string $name,
        public string $token,
        public ?Carbon $lastUsedAt,
        public Carbon $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            token: $data['token'],
            lastUsedAt: $data['last_used_at'] ? Carbon::parse($data['last_used_at']) : null,
            createdAt: Carbon::parse($data['created_at']),
        );
    }
}
