<?php

namespace Meruhook\MeruhookSDK\DTOs;

readonly class AccountOverview
{
    public function __construct(
        public User $user,
        public array $addresses,
        public Usage $usage,
        public Billing $billing,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            user: User::fromArray($data['user']),
            addresses: array_map(
                fn (array $address) => Address::fromArray($address),
                $data['addresses']
            ),
            usage: Usage::fromArray($data['usage']),
            billing: Billing::fromArray($data['billing']),
        );
    }
}
