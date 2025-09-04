<?php

use Illuminate\Support\Facades\Http;
use Meruhook\MeruhookSDK\DTOs\Address;
use Meruhook\MeruhookSDK\Facades\Meru;

it('can use facade to access addresses', function () {
    Http::fake([
        'api.test.com/api/addresses' => Http::response([
            'data' => []
        ])
    ]);

    $addresses = Meru::addresses()->list();

    expect($addresses)->toBeArray();
});

it('can create address via facade', function () {
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

    $address = Meru::addresses()->create('https://webhook.test');

    expect($address)->toBeInstanceOf(Address::class);
    expect($address->webhookUrl)->toBe('https://webhook.test');
});