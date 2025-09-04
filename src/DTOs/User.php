<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class User
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?Carbon $emailVerifiedAt,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            emailVerifiedAt: $data['email_verified_at'] ? Carbon::parse($data['email_verified_at']) : null,
            createdAt: Carbon::parse($data['created_at']),
            updatedAt: Carbon::parse($data['updated_at']),
        );
    }
}
