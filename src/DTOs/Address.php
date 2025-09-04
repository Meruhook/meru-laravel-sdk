<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class Address
{
    public function __construct(
        public string $id,
        public string $address,
        public ?string $webhookUrl,
        public bool $isEnabled,
        public bool $isPermanent,
        public ?Carbon $expiresAt,
        public int $emailCount,
        public ?string $lastReceivedAt,
        public bool $isExpired,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            address: $data['address'],
            webhookUrl: $data['webhook_url'],
            isEnabled: $data['is_enabled'],
            isPermanent: $data['is_permanent'],
            expiresAt: $data['expires_at'] ? Carbon::parse($data['expires_at']) : null,
            emailCount: $data['email_count'],
            lastReceivedAt: $data['last_received_at'],
            isExpired: $data['is_expired'],
            createdAt: Carbon::parse($data['created_at']),
            updatedAt: Carbon::parse($data['updated_at']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'webhook_url' => $this->webhookUrl,
            'is_enabled' => $this->isEnabled,
            'is_permanent' => $this->isPermanent,
            'expires_at' => $this->expiresAt?->toISOString(),
            'email_count' => $this->emailCount,
            'last_received_at' => $this->lastReceivedAt,
            'is_expired' => $this->isExpired,
            'created_at' => $this->createdAt->toISOString(),
            'updated_at' => $this->updatedAt->toISOString(),
        ];
    }
}
