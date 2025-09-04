<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class SpendingLimit
{
    public function __construct(
        public bool $hasLimit,
        public ?float $limit,
        public float $currentSpending,
        public ?float $remainingBudget,
        public ?float $percentageUsed,
        public bool $isOverLimit,
        public ?Carbon $limitReachedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            hasLimit: $data['has_limit'],
            limit: $data['limit'],
            currentSpending: $data['current_spending'],
            remainingBudget: $data['remaining_budget'],
            percentageUsed: $data['percentage_used'],
            isOverLimit: $data['is_over_limit'],
            limitReachedAt: $data['limit_reached_at'] ? Carbon::parse($data['limit_reached_at']) : null,
        );
    }
}
