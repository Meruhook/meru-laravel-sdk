# Meru API SDK Specification

A comprehensive Laravel package specification for building a type-safe, modern SDK for the Meru email webhook service API using Saloon v3.

## Overview

The Meru API SDK provides a fluent, Laravel-friendly interface for interacting with the Meru email webhook service. The service allows users to create temporary and permanent email addresses that forward incoming emails to configured webhook URLs.

## Package Requirements

### Core Dependencies

```json
{
    "php": "^8.1",
    "saloonphp/saloon": "^3.0",
    "illuminate/support": "^10.0|^11.0|^12.0",
    "illuminate/http": "^10.0|^11.0|^12.0",
    "illuminate/contracts": "^10.0|^11.0|^12.0"
}
```

### Dev Dependencies

```json
{
    "orchestra/testbench": "^8.0|^9.0|^10.0",
    "pestphp/pest": "^2.0",
    "pestphp/pest-plugin-laravel": "^2.0",
    "phpunit/phpunit": "^10.0"
}
```

## Package Structure

```
src/
├── MeruConnector.php                    # Main Saloon connector
├── MeruServiceProvider.php              # Laravel service provider
├── Config/
│   └── meru.php                         # Configuration file
├── Requests/
│   ├── Auth/
│   │   ├── GetUserRequest.php           # GET /api/user
│   │   └── CreateApiTokenRequest.php    # POST /api-keys
│   ├── Addresses/
│   │   ├── ListAddressesRequest.php     # GET /api/addresses
│   │   ├── CreateAddressRequest.php     # POST /api/addresses
│   │   ├── GetAddressRequest.php        # GET /api/addresses/{id}
│   │   ├── UpdateAddressRequest.php     # PATCH /api/addresses/{id}
│   │   └── DeleteAddressRequest.php     # DELETE /api/addresses/{id}
│   ├── Usage/
│   │   ├── GetUsageRequest.php          # GET /api/usage
│   │   ├── GetUsageEventsRequest.php    # GET /api/usage/events
│   │   └── GetUsagePeriodRequest.php    # GET /api/usage/{period}
│   ├── Billing/
│   │   ├── GetBillingRequest.php        # GET /api/billing
│   │   └── GetBillingBreakdownRequest.php # GET /api/billing/breakdown
│   └── Account/
│       └── GetAccountOverviewRequest.php # GET /api/account
├── Resources/
│   ├── AddressResource.php              # Address CRUD operations
│   ├── UsageResource.php                # Usage statistics
│   ├── BillingResource.php              # Billing information
│   └── AccountResource.php              # Account overview
├── DTOs/
│   ├── Address.php                      # Email address data object
│   ├── Usage.php                        # Usage statistics data object
│   ├── UsageEvent.php                   # Individual usage event
│   ├── Billing.php                      # Billing information
│   ├── Subscription.php                 # Subscription status
│   ├── SpendingLimit.php                # Spending limit data
│   └── User.php                         # User data object
├── Exceptions/
│   ├── MeruException.php                # Base exception
│   ├── AuthenticationException.php      # Authentication failures
│   ├── RateLimitException.php           # Rate limiting
│   └── ValidationException.php          # Validation errors
├── Auth/
│   └── BearerTokenAuth.php              # Sanctum Bearer token auth
├── Webhooks/
│   ├── WebhookSignature.php             # Webhook signature verification
│   └── IncomingEmailWebhook.php         # Incoming email webhook handler
└── Facades/
    └── Meru.php                         # Laravel facade
```

## API Analysis & Endpoints

### Authentication

**Type**: Laravel Sanctum Bearer Token
**Header**: `Authorization: Bearer {token}`

### Core Endpoints

#### User Information
- **GET /api/user** - Get authenticated user information

#### Address Management
- **GET /api/addresses** - List user's email addresses
- **POST /api/addresses** - Create new email address
- **GET /api/addresses/{id}** - Get specific address
- **PATCH /api/addresses/{id}** - Update address (webhook_url, is_enabled)
- **DELETE /api/addresses/{id}** - Delete address

#### Usage Statistics
- **GET /api/usage** - Current month usage summary
- **GET /api/usage/events** - Recent usage events (audit trail)
- **GET /api/usage/{period}** - Usage for specific period (YYYY-MM)

#### Billing Information
- **GET /api/billing** - Current billing status and costs
- **GET /api/billing/breakdown** - Detailed cost breakdown

#### Account Overview
- **GET /api/account** - Combined overview (addresses, usage, billing)


## Configuration

```php
<?php
// config/meru.php

return [
    /*
    |--------------------------------------------------------------------------
    | Meru API Configuration
    |--------------------------------------------------------------------------
    */

    'base_url' => env('MERU_BASE_URL', 'https://api.meruhook.com'),

    'api_token' => env('MERU_API_TOKEN'),

    'timeout' => env('MERU_TIMEOUT', 30),

    'retry' => [
        'times' => env('MERU_RETRY_TIMES', 3),
        'delay' => env('MERU_RETRY_DELAY', 100), // milliseconds
    ],

    'webhook' => [
        'signature_header' => 'X-Meru-Signature',
        'secret' => env('MERU_WEBHOOK_SECRET'),
        'tolerance' => env('MERU_WEBHOOK_TOLERANCE', 300), // seconds
    ],

    'debug' => env('MERU_DEBUG', false),
];
```

## Core Connector Implementation

```php
<?php

namespace Meruhook\MeruhookSdk;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Traits\Plugins\HasTimeout;
use Saloon\Contracts\Authenticator;
use Meruhook\MeruhookSDK\Auth\BearerTokenAuth;

class MeruConnector extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors, HasTimeout;

    public function __construct(
        protected string $apiToken,
        protected string $baseUrl = 'https://api.meru.io'
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function defaultAuth(): ?Authenticator
    {
        return new BearerTokenAuth($this->apiToken);
    }

    protected function defaultConfig(): array
    {
        return [
            'timeout' => config('meru.timeout', 30),
        ];
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => 'MeruSDK/1.0',
        ];
    }
}
```

## Data Transfer Objects (DTOs)

### Address DTO

```php
<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class Address
{
    public function __construct(
        public string $id,
        public string $address,
        public ?string $webhookUrl,
        public bool $isEnabled,
        public bool $isPermanent,
        public ?Carbon $expiresAt,
        public int $emailCount,
        public ?string $lastReceivedAt,
        public bool $isExpired,
        public Carbon $createdAt,
        public Carbon $updatedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            address: $data['address'],
            webhookUrl: $data['webhook_url'],
            isEnabled: $data['is_enabled'],
            isPermanent: $data['is_permanent'],
            expiresAt: $data['expires_at'] ? Carbon::parse($data['expires_at']) : null,
            emailCount: $data['email_count'],
            lastReceivedAt: $data['last_received_at'],
            isExpired: $data['is_expired'],
            createdAt: Carbon::parse($data['created_at']),
            updatedAt: Carbon::parse($data['updated_at']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'address' => $this->address,
            'webhook_url' => $this->webhookUrl,
            'is_enabled' => $this->isEnabled,
            'is_permanent' => $this->isPermanent,
            'expires_at' => $this->expiresAt?->toISOString(),
            'email_count' => $this->emailCount,
            'last_received_at' => $this->lastReceivedAt,
            'is_expired' => $this->isExpired,
            'created_at' => $this->createdAt->toISOString(),
            'updated_at' => $this->updatedAt->toISOString(),
        ];
    }
}
```

### Usage DTO

```php
<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class Usage
{
    public function __construct(
        public int $totalEmails,
        public int $successfulEmails,
        public int $failedWebhooks,
        public int $todayEmails,
        public int $projectedMonthly,
        public float $successRate,
        public float $failureRate,
        public ?string $lastCalculatedAt,
        public UsagePeriod $period,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalEmails: $data['total_emails'],
            successfulEmails: $data['successful_emails'],
            failedWebhooks: $data['failed_webhooks'],
            todayEmails: $data['today_emails'],
            projectedMonthly: $data['projected_monthly'],
            successRate: $data['success_rate'],
            failureRate: $data['failure_rate'],
            lastCalculatedAt: $data['last_calculated_at'],
            period: UsagePeriod::fromArray($data['period']),
        );
    }
}

readonly class UsagePeriod
{
    public function __construct(
        public Carbon $start,
        public Carbon $end,
        public int $currentDay,
        public int $daysInMonth,
        public int $daysRemaining,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            start: Carbon::parse($data['start']),
            end: Carbon::parse($data['end']),
            currentDay: $data['current_day'],
            daysInMonth: $data['days_in_month'],
            daysRemaining: $data['days_remaining'],
        );
    }
}
```

### Billing DTO

```php
<?php

namespace Meruhook\MeruhookSDK\DTOs;

use Carbon\Carbon;

readonly class Billing
{
    public function __construct(
        public float $currentCost,
        public float $projectedCost,
        public float $emailProcessingCost,
        public Subscription $subscription,
        public SpendingLimit $spendingLimit,
        public BillingPeriod $period,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            currentCost: $data['current_cost'],
            projectedCost: $data['projected_cost'],
            emailProcessingCost: $data['email_processing_cost'],
            subscription: Subscription::fromArray($data['subscription']),
            spendingLimit: SpendingLimit::fromArray($data['spending_limit']),
            period: BillingPeriod::fromArray($data['period']),
        );
    }
}

readonly class Subscription
{
    public function __construct(
        public bool $hasBaseSubscription,
        public bool $hasAddonSubscription,
        public bool $onTrial,
        public ?Carbon $trialEndsAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            hasBaseSubscription: $data['has_base_subscription'],
            hasAddonSubscription: $data['has_addon_subscription'],
            onTrial: $data['on_trial'],
            trialEndsAt: $data['trial_ends_at'] ? Carbon::parse($data['trial_ends_at']) : null,
        );
    }
}

readonly class SpendingLimit
{
    public function __construct(
        public bool $hasLimit,
        public ?float $limit,
        public float $currentSpending,
        public ?float $remainingBudget,
        public ?float $percentageUsed,
        public bool $isOverLimit,
        public ?Carbon $limitReachedAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            hasLimit: $data['has_limit'],
            limit: $data['limit'],
            currentSpending: $data['current_spending'],
            remainingBudget: $data['remaining_budget'],
            percentageUsed: $data['percentage_used'],
            isOverLimit: $data['is_over_limit'],
            limitReachedAt: $data['limit_reached_at'] ? Carbon::parse($data['limit_reached_at']) : null,
        );
    }
}

readonly class BillingPeriod
{
    public function __construct(
        public Carbon $start,
        public Carbon $end,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            start: Carbon::parse($data['start']),
            end: Carbon::parse($data['end']),
        );
    }
}
```

## Request Classes

### Address Requests

```php
<?php

namespace Meruhook\MeruhookSDK\Requests\Addresses;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Meruhook\MeruhookSDK\DTOs\Address;

class ListAddressesRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/addresses';
    }

    public function createDtoFromResponse(Response $response): array
    {
        $data = $response->json('data');

        return array_map(
            fn (array $address) => Address::fromArray($address),
            $data
        );
    }
}

class CreateAddressRequest extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        protected string $webhookUrl,
        protected bool $isPermanent = true,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/addresses';
    }

    protected function defaultBody(): array
    {
        return [
            'webhook_url' => $this->webhookUrl,
            'is_permanent' => $this->isPermanent,
        ];
    }

    public function createDtoFromResponse(Response $response): Address
    {
        return Address::fromArray($response->json('data'));
    }
}

class GetAddressRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $addressId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/addresses/{$this->addressId}";
    }

    public function createDtoFromResponse(Response $response): Address
    {
        return Address::fromArray($response->json('data'));
    }
}

class UpdateAddressRequest extends Request
{
    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $addressId,
        protected ?string $webhookUrl = null,
        protected ?bool $isEnabled = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/addresses/{$this->addressId}";
    }

    protected function defaultBody(): array
    {
        $body = [];

        if ($this->webhookUrl !== null) {
            $body['webhook_url'] = $this->webhookUrl;
        }

        if ($this->isEnabled !== null) {
            $body['is_enabled'] = $this->isEnabled;
        }

        return $body;
    }

    public function createDtoFromResponse(Response $response): Address
    {
        return Address::fromArray($response->json('data'));
    }
}

class DeleteAddressRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        protected string $addressId,
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/addresses/{$this->addressId}";
    }
}
```

### Usage Requests

```php
<?php

namespace Meruhook\MeruhookSDK\Requests\Usage;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Meruhook\MeruhookSDK\DTOs\Usage;
use Meruhook\MeruhookSDK\DTOs\UsageEvent;

class GetUsageRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/api/usage';
    }

    public function createDtoFromResponse(Response $response): Usage
    {
        return Usage::fromArray($response->json('data'));
    }
}

class GetUsageEventsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected int $limit = 50,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/usage/events';
    }

    protected function defaultQuery(): array
    {
        return [
            'limit' => $this->limit,
        ];
    }

    public function createDtoFromResponse(Response $response): array
    {
        $data = $response->json('data');

        return array_map(
            fn (array $event) => UsageEvent::fromArray($event),
            $data
        );
    }
}

class GetUsagePeriodRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $period, // YYYY-MM format
    ) {}

    public function resolveEndpoint(): string
    {
        return "/api/usage/{$this->period}";
    }

    public function createDtoFromResponse(Response $response): Usage
    {
        return Usage::fromArray($response->json('data'));
    }
}
```

## Resource Classes

```php
<?php

namespace Meruhook\MeruhookSDK\Resources;

use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Requests\Addresses\ListAddressesRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\CreateAddressRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\GetAddressRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\UpdateAddressRequest;
use Meruhook\MeruhookSDK\Requests\Addresses\DeleteAddressRequest;
use Meruhook\MeruhookSDK\DTOs\Address;

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
```

## Authentication Implementation

```php
<?php

namespace Meruhook\MeruhookSDK\Auth;

use Saloon\Contracts\Authenticator;
use Saloon\Http\PendingRequest;

class BearerTokenAuth implements Authenticator
{
    public function __construct(
        protected string $token,
    ) {}

    public function set(PendingRequest $pendingRequest): void
    {
        $pendingRequest->headers()->add('Authorization', 'Bearer ' . $this->token);
    }
}
```

## Exception Handling

```php
<?php

namespace Meruhook\MeruhookSDK\Exceptions;

use Exception;
use Saloon\Http\Response;

class MeruException extends Exception
{
    public function __construct(
        string $message,
        protected ?Response $response = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public static function fromResponse(Response $response): static
    {
        $data = $response->json();
        $message = $data['message'] ?? 'An error occurred';

        return new static($message, $response, $response->status());
    }
}

class AuthenticationException extends MeruException {}
class ValidationException extends MeruException {}
class RateLimitException extends MeruException {}
```

## Webhook Handling

```php
<?php

namespace Meruhook\MeruhookSDK\Webhooks;

use Illuminate\Http\Request;
use Meruhook\MeruhookSDK\Exceptions\MeruException;

readonly class IncomingEmailWebhook
{
    public function __construct(
        public string $messageId,
        public string $from,
        public array $to,
        public string $subject,
        public ?string $textContent,
        public ?string $htmlContent,
        public array $attachments,
        public array $headers,
        public int $size,
        public string $timestamp,
    ) {}

    public static function fromRequest(Request $request): self
    {
        // Verify webhook signature first
        WebhookSignature::verify($request);

        return new self(
            messageId: $request->input('message_id'),
            from: $request->input('from'),
            to: $request->input('to', []),
            subject: $request->input('subject', ''),
            textContent: $request->input('text_content'),
            htmlContent: $request->input('html_content'),
            attachments: $request->input('attachments', []),
            headers: $request->input('headers', []),
            size: $request->input('size', 0),
            timestamp: $request->input('timestamp'),
        );
    }
}

class WebhookSignature
{
    public static function verify(Request $request): void
    {
        $signature = $request->header('X-Meru-Signature');
        $secret = config('meru.webhook.secret');
        $tolerance = config('meru.webhook.tolerance', 300);

        if (!$signature || !$secret) {
            throw new MeruException('Webhook signature verification failed');
        }

        $payload = $request->getContent();
        $timestamp = $request->header('X-Meru-Timestamp');

        if (!$timestamp || abs(time() - $timestamp) > $tolerance) {
            throw new MeruException('Webhook timestamp is too old');
        }

        $expectedSignature = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            throw new MeruException('Webhook signature verification failed');
        }
    }
}
```

## Laravel Service Provider

```php
<?php

namespace Meruhook\MeruhookSDK;

use Illuminate\Support\ServiceProvider;
use Meruhook\MeruhookSDK\MeruConnector;

class MeruServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/meru.php', 'meru');

        $this->app->singleton(MeruConnector::class, function () {
            return new MeruConnector(
                apiToken: config('meru.api_token'),
                baseUrl: config('meru.base_url')
            );
        });

        $this->app->alias(MeruConnector::class, 'meru');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/meru.php' => config_path('meru.php'),
            ], 'config');
        }
    }
}
```

## Laravel Facade

```php
<?php

namespace Meruhook\MeruhookSDK\Facades;

use Illuminate\Support\Facades\Facade;
use Meruhook\MeruhookSDK\Resources\AddressResource;
use Meruhook\MeruhookSDK\Resources\UsageResource;
use Meruhook\MeruhookSDK\Resources\BillingResource;
use Meruhook\MeruhookSDK\Resources\AccountResource;

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
```

## Usage Examples

### Basic Setup

```php
// In your Laravel app
use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Resources\AddressResource;

// Via dependency injection
class EmailWebhookService
{
    public function __construct(
        private MeruConnector $meru
    ) {}

    public function createWebhookEndpoint(string $webhookUrl): Address
    {
        $addresses = new AddressResource($this->meru);
        return $addresses->create($webhookUrl);
    }
}

// Via facade
use Meruhook\MeruhookSDK\Facades\Meru;

$address = Meru::addresses()->create('https://myapp.com/webhook');
$usage = Meru::usage()->get();
$billing = Meru::billing()->get();
```

### Advanced Usage

```php
use Meruhook\MeruhookSDK\Facades\Meru;

// Create temporary address that expires
$address = Meru::addresses()->create(
    webhookUrl: 'https://myapp.com/webhook',
    isPermanent: false
);

// Manage address states
Meru::addresses()->disable($address->id);
Meru::addresses()->enable($address->id);
Meru::addresses()->updateWebhookUrl($address->id, 'https://newwebhook.com');

// Get usage statistics
$currentUsage = Meru::usage()->get();
$usageEvents = Meru::usage()->events(limit: 100);
$lastMonthUsage = Meru::usage()->period('2024-01');

// Get billing information
$billing = Meru::billing()->get();
$breakdown = Meru::billing()->breakdown();

// Get combined account overview
$account = Meru::account()->overview();
```

### Webhook Handling

```php
// In your Laravel controller
use Meruhook\MeruhookSDK\Webhooks\IncomingEmailWebhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleIncomingEmail(Request $request)
    {
        try {
            $email = IncomingEmailWebhook::fromRequest($request);

            // Process the email
            $this->processIncomingEmail($email);

            return response()->json(['status' => 'success']);
        } catch (MeruException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function processIncomingEmail(IncomingEmailWebhook $email): void
    {
        // Your business logic here
        logger('Received email', [
            'message_id' => $email->messageId,
            'from' => $email->from,
            'subject' => $email->subject,
        ]);
    }
}
```

## Testing Strategy

### Unit Tests

```php
<?php

use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Resources\AddressResource;
use Meruhook\MeruhookSDK\DTOs\Address;

it('can create an address', function () {
    $connector = new MeruConnector('test-token', 'https://api.test');
    $addresses = new AddressResource($connector);

    // Mock the HTTP response
    Http::fake([
        'api.test/api/addresses' => Http::response([
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
```

### Integration Tests

```php
<?php

it('can make real API calls', function () {
    $connector = new MeruConnector(
        apiToken: config('meru.test_token'),
        baseUrl: config('meru.test_base_url')
    );

    $addresses = new AddressResource($connector);

    // Test creating address
    $address = $addresses->create('https://webhook-test.com');
    expect($address->id)->not->toBeEmpty();

    // Test updating address
    $updatedAddress = $addresses->updateWebhookUrl($address->id, 'https://new-webhook.com');
    expect($updatedAddress->webhookUrl)->toBe('https://new-webhook.com');

    // Test deleting address
    $addresses->delete($address->id);

    // Verify it's deleted
    expect(fn() => $addresses->get($address->id))->toThrow(MeruException::class);
})->group('integration');
```

## Documentation Requirements

### README.md Structure

1. **Installation** - Composer installation and setup
2. **Configuration** - Laravel config publishing and environment variables
3. **Quick Start** - Basic usage examples
4. **Authentication** - API token setup
5. **Resources** - Detailed documentation for each resource
6. **DTOs** - Data transfer object documentation
7. **Webhook Handling** - Complete webhook setup guide
8. **Error Handling** - Exception types and handling
9. **Testing** - Testing examples and best practices
10. **Contributing** - Development setup and contribution guidelines

### API Documentation

- Complete PHPDoc annotations for all classes and methods
- Type hints for all parameters and return types
- Usage examples in docblocks
- Links to official API documentation where applicable

### Laravel Package Features

- **Service Provider**: Automatic registration of services
- **Facade**: Clean Laravel-style API access
- **Configuration**: Publishable config file
- **Middleware**: Optional webhook signature verification middleware
- **Artisan Commands**: Optional commands for API management
- **Events**: Laravel events for webhook processing
- **Queue Integration**: Background job support for webhook processing

## Deployment & Distribution

### Composer Package

```json
{
    "name": "meruhook/meruhook-sdk",
    "description": "Laravel SDK for Meru Email Webhook Service",
    "type": "library",
    "keywords": ["laravel", "meru", "email", "webhook", "sdk"],
    "license": "MIT",
    "authors": [
        {
            "name": "Zachary Oakes",
            "email": "zachary.oakes@gmail.com"
        }
    ],
    "require": {
        "php": "^8.1",
        "saloonphp/saloon": "^3.0",
        "illuminate/support": "^10.0|^11.0|^12.0"
    },
    "autoload": {
        "psr-4": {
            "Meruhook\\MeruhookSDK\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Meruhook\\MeruhookSDK\\MeruServiceProvider"
            ],
            "aliases": {
                "Meru": "Meruhook\\MeruhookSDK\\Facades\\Meru"
            }
        }
    },
    "minimum-stability": "stable"
}
```

### GitHub Actions CI/CD

- **Tests**: PHPUnit/Pest tests across PHP 8.1, 8.2, 8.3
- **Static Analysis**: PHPStan level 8
- **Code Style**: Laravel Pint
- **Security**: Security advisories check
- **Documentation**: Automatic API documentation generation

## Additional Features

### Rate Limiting Support
- Automatic retry with exponential backoff
- Rate limit exception handling
- Configurable retry policies

### Logging & Debugging
- PSR-3 logger integration
- Debug mode for request/response logging
- Structured logging with context

### Caching Support
- Optional response caching for usage/billing data
- Laravel cache integration
- Configurable TTL

### Event System
- Laravel events for all API operations
- Webhook received events
- Error events for monitoring

This specification provides a comprehensive foundation for building a professional, type-safe Laravel SDK for the Meru API using Saloon v3. The resulting package will be maintainable, well-tested, and follow Laravel best practices.
