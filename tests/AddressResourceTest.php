<?php

use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Requests\Addresses\CreateAddressRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\ListAddressesRequest;
use Meruhook\MeruhookSDK\Resources\AddressResource;

it('can instantiate address resource', function () {
    $connector = new MeruConnector('test-token', 'https://api.test.com');
    $addresses = new AddressResource($connector);

    expect($addresses)->toBeInstanceOf(AddressResource::class);
});

it('creates correct request for new address', function () {
    $request = new CreateAddressRequest('https://webhook.test', true);

    expect($request->resolveEndpoint())->toBe('/api/addresses');
});

it('creates correct request for listing addresses', function () {
    $request = new ListAddressesRequest;

    expect($request->resolveEndpoint())->toBe('/api/addresses');
});
