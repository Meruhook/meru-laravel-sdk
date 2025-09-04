<?php

namespace Meruhook\MeruhookSDK\Webhooks;

use Illuminate\Http\Request;

class IncomingEmailWebhook
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
