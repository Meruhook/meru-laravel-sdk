<?php

use Illuminate\Support\Facades\Http;
use Meruhook\MeruhookSDK\DTOs\Address;
use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Resources\AddressResource;

it('can create an address', function () {
    $connector = new MeruConnector('test-token', 'https://api.test.com');
    $addresses = new AddressResource($connector);

    Http::fake([
        'api.test.com/api/addresses' => Http::response([
            'data' => [
                'id' => 'addr_123',
                'address' => 'test@example.com',
                'webhook_url' => 'https://webhook.test',
                'is_enabled' => true,
                'is_permanent' => true,
                'expires_at' => null,
                'email_count' => 0,
                'last_received_at' => null,
                'is_expired' => false,
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
            ]
        ])
    ]);

    $address = $addresses->create('https://webhook.test');

    expect($address)->toBeInstanceOf(Address::class);
    expect($address->id)->toBe('addr_123');
    expect($address->webhookUrl)->toBe('https://webhook.test');
});

it('can list addresses', function () {
    $connector = new MeruConnector('test-token', 'https://api.test.com');
    $addresses = new AddressResource($connector);

    Http::fake([
        'api.test.com/api/addresses' => Http::response([
            'data' => [
                [
                    'id' => 'addr_123',
                    'address' => 'test1@example.com',
                    'webhook_url' => 'https://webhook.test',
                    'is_enabled' => true,
                    'is_permanent' => true,
                    'expires_at' => null,
                    'email_count' => 0,
                    'last_received_at' => null,
                    'is_expired' => false,
                    'created_at' => now()->toISOString(),
                    'updated_at' => now()->toISOString(),
                ],
                [
                    'id' => 'addr_456',
                    'address' => 'test2@example.com',
                    'webhook_url' => 'https://webhook2.test',
                    'is_enabled' => false,
                    'is_permanent' => false,
                    'expires_at' => now()->addHours(24)->toISOString(),
                    'email_count' => 5,
                    'last_received_at' => now()->subHours(2)->toISOString(),
                    'is_expired' => false,
                    'created_at' => now()->subDays(7)->toISOString(),
                    'updated_at' => now()->subHours(1)->toISOString(),
                ]
            ]
        ])
    ]);

    $addressList = $addresses->list();

    expect($addressList)->toHaveCount(2);
    expect($addressList[0])->toBeInstanceOf(Address::class);
    expect($addressList[0]->id)->toBe('addr_123');
    expect($addressList[1]->id)->toBe('addr_456');
});