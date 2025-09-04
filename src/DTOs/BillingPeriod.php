<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class BillingPeriod
{
    public function __construct(
        public Carbon $start,
        public Carbon $end,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            start: Carbon::parse($data['start']),
            end: Carbon::parse($data['end']),
        );
    }
}