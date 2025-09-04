<?php

use Meruhook\MeruhookSDK\Facades\Meru;
use Meruhook\MeruhookSDK\Resources\AccountResource;
use Meruhook\MeruhookSDK\Resources\AddressResource;
use Meruhook\MeruhookSDK\Resources\BillingResource;
use Meruhook\MeruhookSDK\Resources\UsageResource;

it('can access address resource via facade', function () {
    $addresses = Meru::addresses();

    expect($addresses)->toBeInstanceOf(AddressResource::class);
});

it('can access usage resource via facade', function () {
    $usage = Meru::usage();

    expect($usage)->toBeInstanceOf(UsageResource::class);
});

it('can access billing resource via facade', function () {
    $billing = Meru::billing();

    expect($billing)->toBeInstanceOf(BillingResource::class);
});

it('can access account resource via facade', function () {
    $account = Meru::account();

    expect($account)->toBeInstanceOf(AccountResource::class);
});
