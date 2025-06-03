<?php

namespace App\Services;

use App\Contracts\NotifyServiceInterface;
use Psr\Log\LoggerInterface;

class NotifyService implements NotifyServiceInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function notify(string $receiver, string $subject, string $body): void
    {
        $this->logger->info('Notification sent', [
            'receiver' => $receiver,
            'subject' => $subject,
            'body' => $body,
            'sent_at' => now()->toDateTimeString()
        ]);
    }
}
