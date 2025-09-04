<?php

namespace Meruhook\MeruhookSDK\Resources;

use Meruhook\MeruhookSDK\DTOs\Billing;
use Meruhook\MeruhookSDK\DTOs\BillingBreakdown;
use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Requests\Billing\GetBillingBreakdownRequest;
use Meruhook\MeruhookSDK\Requests\Billing\GetBillingRequest;

class BillingResource
{
    public function __construct(
        protected MeruConnector $connector,
    ) {}

    /**
     * Get current billing status and costs
     */
    public function get(): Billing
    {
        $request = new GetBillingRequest;
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Get detailed cost breakdown
     */
    public function breakdown(): BillingBreakdown
    {
        $request = new GetBillingBreakdownRequest;
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }
}
