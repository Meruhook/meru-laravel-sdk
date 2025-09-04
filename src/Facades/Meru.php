<?php

namespace Meruhook\MeruhookSDK\Facades;

use Illuminate\Support\Facades\Facade;
use Meruhook\MeruhookSDK\Resources\AccountResource;
use Meruhook\MeruhookSDK\Resources\AddressResource;
use Meruhook\MeruhookSDK\Resources\BillingResource;
use Meruhook\MeruhookSDK\Resources\UsageResource;

/**
 * @method static AddressResource addresses()
 * @method static UsageResource usage()
 * @method static BillingResource billing()
 * @method static AccountResource account()
 */
class Meru extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'meru';
    }
}
