# Meru Laravel SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/meruhook/meruhook-sdk.svg?style=flat-square)](https://packagist.org/packages/meruhook/meruhook-sdk)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/meruhook/meruhook-sdk/run-tests?label=tests)](https://github.com/meruhook/meruhook-sdk/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/meruhook/meruhook-sdk/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/meruhook/meruhook-sdk/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![GitHub PHPStan Action Status](https://img.shields.io/github/workflow/status/meruhook/meruhook-sdk/PHPStan?label=phpstan)](https://github.com/meruhook/meruhook-sdk/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/meruhook/meruhook-sdk.svg?style=flat-square)](https://packagist.org/packages/meruhook/meruhook-sdk)

A comprehensive Laravel package providing a type-safe, modern SDK for the Meru email webhook service API using Saloon v3.

## Overview

The Meru API SDK provides a fluent, Laravel-friendly interface for interacting with the Meru email webhook service. The service allows users to create temporary and permanent email addresses that forward incoming emails to configured webhook URLs.

## Requirements

- PHP 8.2 or higher
- Laravel 10.0, 11.0, or 12.0
- Saloon v3

## Installation

You can install the package via composer:

```bash
composer require meruhook/meruhook-sdk
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="meru-config"
```

This is the contents of the published config file:

```php
return [
    'base_url' => env('MERU_BASE_URL', 'https://api.meruhook.com'),
    'api_token' => env('MERU_API_TOKEN'),
    'timeout' => env('MERU_TIMEOUT', 30),
    'retry' => [
        'times' => env('MERU_RETRY_TIMES', 3),
        'delay' => env('MERU_RETRY_DELAY', 100),
    ],
   
    'debug' => env('MERU_DEBUG', false),
];
```

## Configuration

Add your Meru API credentials to your `.env` file:

```env
MERU_API_TOKEN=your_api_token_here
```

## Quick Start

### Basic Usage with Facade

```php
use Meruhook\MeruhookSDK\Facades\Meru;

// Create a new email address
$address = Meru::addresses()->create('https://myapp.com/webhook');

// List all addresses
$addresses = Meru::addresses()->list();

// Get usage statistics
$usage = Meru::usage()->get();

// Get billing information
$billing = Meru::billing()->get();
```

### Dependency Injection

```php
use Meruhook\MeruhookSDK\MeruConnector;
use Meruhook\MeruhookSDK\Resources\AddressResource;

class EmailWebhookService
{
    public function __construct(
        private MeruConnector $meru
    ) {}

    public function createWebhookEndpoint(string $webhookUrl): Address
    {
        return $this->meru->addresses()->create($webhookUrl);
    }
}
```

## Address Management

### Creating Addresses

```php
// Create permanent address
$address = Meru::addresses()->create('https://myapp.com/webhook');

// Create temporary address
$address = Meru::addresses()->create('https://myapp.com/webhook', isPermanent: false);
```

### Managing Addresses

```php
// Get specific address
$address = Meru::addresses()->get('addr_123');

// Update webhook URL
$address = Meru::addresses()->updateWebhookUrl('addr_123', 'https://newwebhook.com');

// Enable/disable addresses
$address = Meru::addresses()->enable('addr_123');
$address = Meru::addresses()->disable('addr_123');

// Delete address
Meru::addresses()->delete('addr_123');
```

## Usage Statistics

```php
// Get current month usage
$usage = Meru::usage()->get();

// Get usage events (audit trail)
$events = Meru::usage()->events(limit: 100);

// Get usage for specific period
$usage = Meru::usage()->period('2024-01');

// Access usage data
echo "Total emails: {$usage->totalEmails}";
echo "Success rate: {$usage->successRate}%";
echo "Today's emails: {$usage->todayEmails}";
```

## Billing Information

```php
// Get current billing status
$billing = Meru::billing()->get();

// Get detailed cost breakdown
$breakdown = Meru::billing()->breakdown();

// Access billing data
echo "Current cost: ${$billing->currentCost}";
echo "Projected cost: ${$billing->projectedCost}";
echo "On trial: " . ($billing->subscription->onTrial ? 'Yes' : 'No');
```

## Account Information

```php
// Get user information
$user = Meru::account()->user();

// Get combined account overview
$account = Meru::account()->overview();

// Create API token
$token = Meru::account()->createApiToken('My App Token');
```


## Data Transfer Objects (DTOs)

All API responses are returned as strongly-typed DTOs:

### Address DTO

```php
$address->id;              // string
$address->address;         // string (email@example.com)
$address->webhookUrl;      // ?string
$address->isEnabled;       // bool
$address->isPermanent;     // bool
$address->expiresAt;       // ?Carbon
$address->emailCount;      // int
$address->isExpired;       // bool
$address->createdAt;       // Carbon
$address->updatedAt;       // Carbon
```

### Usage DTO

```php
$usage->totalEmails;       // int
$usage->successfulEmails;  // int
$usage->failedWebhooks;    // int
$usage->todayEmails;       // int
$usage->projectedMonthly;  // int
$usage->successRate;       // float
$usage->failureRate;       // float
$usage->period;            // UsagePeriod DTO
```

### Billing DTO

```php
$billing->currentCost;        // float
$billing->projectedCost;      // float
$billing->emailProcessingCost;// float
$billing->subscription;       // Subscription DTO
$billing->spendingLimit;      // SpendingLimit DTO
$billing->period;             // BillingPeriod DTO
```

## Error Handling

The SDK provides specific exception types for different error scenarios:

```php
use Meruhook\MeruhookSDK\Exceptions\{
    MeruException,
    AuthenticationException,
    ValidationException,
    RateLimitException
};

try {
    $address = Meru::addresses()->create('invalid-url');
} catch (ValidationException $e) {
    // Handle validation errors
    echo "Validation failed: " . $e->getMessage();
} catch (AuthenticationException $e) {
    // Handle authentication errors
    echo "Authentication failed: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Handle rate limiting
    echo "Rate limited: " . $e->getMessage();
} catch (MeruException $e) {
    // Handle general API errors
    echo "API error: " . $e->getMessage();
}
```

## Development

### Testing

```bash
composer test
```

### Code Quality

Run PHPStan analysis:
```bash
composer analyse
```

Format code with Laravel Pint:
```bash
composer format
```

### Releasing

This package uses automated releases via GitHub Actions. 

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Zachary Oakes](https://github.com/zacharyoakes)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.