<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class UsagePeriod
{
    public function __construct(
        public Carbon $start,
        public Carbon $end,
        public int $currentDay,
        public int $daysInMonth,
        public int $daysRemaining,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            start: Carbon::parse($data['start']),
            end: Carbon::parse($data['end']),
            currentDay: (int) $data['current_day'],
            daysInMonth: (int) $data['days_in_month'],
            daysRemaining: (int) $data['days_remaining'],
        );
    }
}
