<?php

use Carbon\Carbon;
use Meruhook\MeruhookSDK\DTOs\Address;

it('can create address dto from array', function () {
    $data = [
        'id' => 'addr_123',
        'address' => 'test@example.com',
        'webhook_url' => 'https://webhook.test',
        'is_enabled' => true,
        'is_permanent' => true,
        'expires_at' => null,
        'email_count' => 0,
        'last_received_at' => null,
        'is_expired' => false,
        'created_at' => '2024-01-01T12:00:00Z',
        'updated_at' => '2024-01-01T12:00:00Z',
    ];

    $address = Address::fromArray($data);

    expect($address)->toBeInstanceOf(Address::class);
    expect($address->id)->toBe('addr_123');
    expect($address->address)->toBe('test@example.com');
    expect($address->webhookUrl)->toBe('https://webhook.test');
    expect($address->isEnabled)->toBeTrue();
    expect($address->isPermanent)->toBeTrue();
    expect($address->emailCount)->toBe(0);
    expect($address->isExpired)->toBeFalse();
    expect($address->createdAt)->toBeInstanceOf(Carbon::class);
});

it('can convert address dto to array', function () {
    $address = Address::fromArray([
        'id' => 'addr_123',
        'address' => 'test@example.com',
        'webhook_url' => 'https://webhook.test',
        'is_enabled' => true,
        'is_permanent' => true,
        'expires_at' => null,
        'email_count' => 5,
        'last_received_at' => null,
        'is_expired' => false,
        'created_at' => '2024-01-01T12:00:00Z',
        'updated_at' => '2024-01-01T12:00:00Z',
    ]);

    $array = $address->toArray();

    expect($array)->toBeArray();
    expect($array['id'])->toBe('addr_123');
    expect($array['webhook_url'])->toBe('https://webhook.test');
    expect($array['email_count'])->toBe(5);
    expect($array['created_at'])->toBeString();
});
