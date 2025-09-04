<?php

use Illuminate\Http\Request;
use Meruhook\MeruhookSDK\Exceptions\MeruException;
use Meruhook\MeruhookSDK\Webhooks\IncomingEmailWebhook;
use Meruhook\MeruhookSDK\Webhooks\WebhookSignature;

it('can verify webhook signature', function () {
    $secret = 'test-secret';
    $payload = json_encode(['message' => 'test']);
    $timestamp = time();
    $signature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

    $request = Request::create('/webhook', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
    ], $payload);

    $request->headers->set('X-Meru-Signature', $signature);
    $request->headers->set('X-Meru-Timestamp', $timestamp);

    config(['meru.webhook.secret' => $secret]);

    expect(function () use ($request) {
        WebhookSignature::verify($request);
    })->not->toThrow();
});

it('throws exception for invalid signature', function () {
    $payload = json_encode(['message' => 'test']);
    $timestamp = time();

    $request = Request::create('/webhook', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
    ], $payload);

    $request->headers->set('X-Meru-Signature', 'invalid-signature');
    $request->headers->set('X-Meru-Timestamp', $timestamp);

    config(['meru.webhook.secret' => 'test-secret']);

    expect(function () use ($request) {
        WebhookSignature::verify($request);
    })->toThrow(MeruException::class);
});

it('can parse incoming email webhook', function () {
    $secret = 'test-secret';
    $payload = json_encode([
        'message_id' => 'msg_123',
        'from' => 'test@example.com',
        'to' => ['webhook@test.com'],
        'subject' => 'Test Subject',
        'text_content' => 'Test content',
        'html_content' => '<p>Test content</p>',
        'attachments' => [],
        'headers' => ['X-Test' => 'value'],
        'size' => 1024,
        'timestamp' => '2024-01-01T12:00:00Z',
    ]);
    $timestamp = time();
    $signature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

    $request = Request::create('/webhook', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
    ], $payload);

    $request->headers->set('X-Meru-Signature', $signature);
    $request->headers->set('X-Meru-Timestamp', $timestamp);

    config(['meru.webhook.secret' => $secret]);

    $webhook = IncomingEmailWebhook::fromRequest($request);

    expect($webhook->messageId)->toBe('msg_123');
    expect($webhook->from)->toBe('test@example.com');
    expect($webhook->subject)->toBe('Test Subject');
    expect($webhook->size)->toBe(1024);
});
