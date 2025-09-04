<?php

namespace Meruhook\MeruhookSDK\DTOs;

readonly class BillingBreakdown
{
    public function __construct(
        public float $emailProcessingCost,
        public int $emailCount,
        public float $averageCostPerEmail,
        public array $dailyBreakdown,
        public BillingPeriod $period,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            emailProcessingCost: $data['email_processing_cost'],
            emailCount: $data['email_count'],
            averageCostPerEmail: $data['average_cost_per_email'],
            dailyBreakdown: $data['daily_breakdown'],
            period: BillingPeriod::fromArray($data['period']),
        );
    }
}