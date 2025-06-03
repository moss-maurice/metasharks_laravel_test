<?php

namespace Tests\Unit;

use App\Contracts\NotifyServiceInterface;
use App\Services\NotifyService;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class NotifyServiceTest extends TestCase
{
    private NotifyService $service;
    private LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->service = new NotifyService($this->loggerMock);
    }

    public function test_notify_logs_correct_information()
    {
        $receiver = 'user@example.com';
        $subject = 'Test Subject';
        $body = 'Test notification body';

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with(
                'Notification sent',
                $this->callback(function ($context) use ($receiver, $subject, $body) {
                    return ($context['receiver'] === $receiver)
                        && ($context['subject'] === $subject)
                        && ($context['body'] === $body)
                        && isset($context['sent_at']);
                })
            );

        $this->service->notify($receiver, $subject, $body);
    }

    public function test_service_implements_interface()
    {
        $this->assertInstanceOf(
            NotifyServiceInterface::class,
            $this->service,
            'Service should implement NotifyServiceInterface'
        );
    }

    public function test_notify_logs_with_empty_body()
    {
        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with('Notification sent', $this->callback(function ($context) {
                return $context['body'] === '';
            }));

        $this->service->notify('test@example.com', 'Subject', '');
    }
}
