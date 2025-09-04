<?php

namespace Meruhook\MeruhookSDK\DTOs;

readonly class Usage
{
    public function __construct(
        public int $totalEmails,
        public int $successfulEmails,
        public int $failedWebhooks,
        public int $todayEmails,
        public int $projectedMonthly,
        public float $successRate,
        public float $failureRate,
        public ?string $lastCalculatedAt,
        public UsagePeriod $period,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalEmails: $data['total_emails'],
            successfulEmails: $data['successful_emails'],
            failedWebhooks: $data['failed_webhooks'],
            todayEmails: $data['today_emails'],
            projectedMonthly: $data['projected_monthly'],
            successRate: $data['success_rate'],
            failureRate: $data['failure_rate'],
            lastCalculatedAt: $data['last_calculated_at'],
            period: UsagePeriod::fromArray($data['period']),
        );
    }
}
