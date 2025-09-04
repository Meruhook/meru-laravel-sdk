<?php

namespace Meruhook\MeruhookSDK\DTOs;

readonly class Billing
{
    public function __construct(
        public float $currentCost,
        public float $projectedCost,
        public float $emailProcessingCost,
        public Subscription $subscription,
        public SpendingLimit $spendingLimit,
        public BillingPeriod $period,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            currentCost: $data['current_cost'],
            projectedCost: $data['projected_cost'],
            emailProcessingCost: $data['email_processing_cost'],
            subscription: Subscription::fromArray($data['subscription']),
            spendingLimit: SpendingLimit::fromArray($data['spending_limit']),
            period: BillingPeriod::fromArray($data['period']),
        );
    }
}
