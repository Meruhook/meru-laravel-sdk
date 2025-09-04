<?php

namespace Meruhook\MeruhookSDK\Resources;

use Meruhook\MeruhookSDK\DTOs\Address;
use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Requests\Addresses\CreateAddressRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\DeleteAddressRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\GetAddressRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\ListAddressesRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\UpdateAddressRequest;

class AddressResource
{
    public function __construct(
        protected MeruConnector $connector,
    ) {}

    /**
     * List all addresses for the authenticated user
     *
     * @return Address[]
     */
    public function list(): array
    {
        $request = new ListAddressesRequest();
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Create a new email address
     */
    public function create(string $webhookUrl, bool $isPermanent = true): Address
    {
        $request = new CreateAddressRequest($webhookUrl, $isPermanent);
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Get a specific address by ID
     */
    public function get(string $addressId): Address
    {
        $request = new GetAddressRequest($addressId);
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Update an existing address
     */
    public function update(
        string $addressId,
        ?string $webhookUrl = null,
        ?bool $isEnabled = null
    ): Address {
        $request = new UpdateAddressRequest($addressId, $webhookUrl, $isEnabled);
        $response = $this->connector->send($request);

        return $request->createDtoFromResponse($response);
    }

    /**
     * Delete an address
     */
    public function delete(string $addressId): void
    {
        $request = new DeleteAddressRequest($addressId);
        $this->connector->send($request);
    }

    /**
     * Enable an address
     */
    public function enable(string $addressId): Address
    {
        return $this->update($addressId, isEnabled: true);
    }

    /**
     * Disable an address
     */
    public function disable(string $addressId): Address
    {
        return $this->update($addressId, isEnabled: false);
    }

    /**
     * Update webhook URL for an address
     */
    public function updateWebhookUrl(string $addressId, string $webhookUrl): Address
    {
        return $this->update($addressId, webhookUrl: $webhookUrl);
    }
}