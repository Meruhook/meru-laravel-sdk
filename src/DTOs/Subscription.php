<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class Subscription
{
    public function __construct(
        public bool $hasBaseSubscription,
        public bool $hasAddonSubscription,
        public bool $onTrial,
        public ?Carbon $trialEndsAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            hasBaseSubscription: $data['has_base_subscription'],
            hasAddonSubscription: $data['has_addon_subscription'],
            onTrial: $data['on_trial'],
            trialEndsAt: $data['trial_ends_at'] ? Carbon::parse($data['trial_ends_at']) : null,
        );
    }
}
