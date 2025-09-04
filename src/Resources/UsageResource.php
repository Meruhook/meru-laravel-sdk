<?php

namespace Meruhook\MeruhookSDK\Resources;

use Meruhook\MeruhookSDK\DTOs\Usage;
use Meruhook\MeruhookSDK\DTOs\UsageEvent;
use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Requests\Usage\GetUsageEventsRequest;
use Meruhook\MeruhookSDK\Requests\Usage\GetUsagePeriodRequest;
use Meruhook\MeruhookSDK\Requests\Usage\GetUsageRequest;

class UsageResource
{
    public function __construct(
        protected MeruConnector $connector,
    ) {}

    /**
     * Get current month usage summary
     */
    public function get(): Usage
    {
        $request = new GetUsageRequest;
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Get recent usage events (audit trail)
     *
     * @return UsageEvent[]
     */
    public function events(int $limit = 50): array
    {
        $request = new GetUsageEventsRequest($limit);
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Get usage for specific period (YYYY-MM format)
     */
    public function period(string $period): Usage
    {
        $request = new GetUsagePeriodRequest($period);
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }
}
