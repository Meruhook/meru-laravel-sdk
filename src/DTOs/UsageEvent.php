<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class UsageEvent
{
    public function __construct(
        public string $id,
        public string $type,
        public string $addressId,
        public string $address,
        public string $messageId,
        public string $from,
        public string $subject,
        public bool $success,
        public ?string $error,
        public ?string $webhookUrl,
        public ?int $webhookResponseCode,
        public int $emailSize,
        public Carbon $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            type: $data['type'],
            addressId: $data['address_id'],
            address: $data['address'],
            messageId: $data['message_id'],
            from: $data['from'],
            subject: $data['subject'],
            success: $data['success'],
            error: $data['error'] ?? null,
            webhookUrl: $data['webhook_url'] ?? null,
            webhookResponseCode: $data['webhook_response_code'] ?? null,
            emailSize: $data['email_size'],
            createdAt: Carbon::parse($data['created_at']),
        );
    }
}
