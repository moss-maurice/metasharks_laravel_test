<?php

namespace App\Contracts;

interface NotifyServiceInterface
{
    public function notify(string $receiver, string $subject, string $body): void;
}