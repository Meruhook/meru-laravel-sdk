<?php

namespace Meruhook\MeruhookSDK\Webhooks;

use Illuminate\Http\Request;
use Meruhook\MeruhookSDK\Exceptions\MeruException;

class WebhookSignature
{
    public static function verify(Request $request): void
    {
        $signature = $request->header('X-Meru-Signature');
        $secret = config('meru.webhook.secret');
        $tolerance = config('meru.webhook.tolerance', 300);

        if (! $signature || ! $secret) {
            throw new MeruException('Webhook signature verification failed');
        }

        $payload = $request->getContent();
        $timestamp = $request->header('X-Meru-Timestamp');

        if (! $timestamp || abs(time() - $timestamp) > $tolerance) {
            throw new MeruException('Webhook timestamp is too old');
        }

        $expectedSignature = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw new MeruException('Webhook signature verification failed');
        }
    }
}
