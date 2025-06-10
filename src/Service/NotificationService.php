<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SensorReading;
use App\Repository\PushSubscriptionRepository;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Psr\Log\LoggerInterface;

final class NotificationService
{
    private $webPush;

    public function __construct(
        private PushSubscriptionRepository $pushSubscriptionRepository,
        private ?LoggerInterface $logger = null,
    )
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => 'mailto:your-email@example.com', // Change this to your actual email
                'publicKey' => 'BEbDsREdM4x6IuftaABaRV_mQ3mhLJ7c3LVSH9gaJUXo8qTDz-YSQ2zwZ0gG8jU5mx-bMewYF_MupIb1S7C4fck',
                'privateKey' => 'bMjUjBENZRqMQnJQB6O8owURpfD2gGB1tsrOpNQz6jw',
            ],
        ]);
    }

    public function sendNotifications(SensorReading $sensorReading): void
    {
        error_log("DEBUG: NotificationService::sendNotifications called for sensor: " . $sensorReading->getSensor()->getHwid() . " with level: " . $sensorReading->getLevel() . " dB");
        
        $pushSubscriptions = $this->pushSubscriptionRepository->findAll();
        
        error_log("DEBUG: Found " . count($pushSubscriptions) . " push subscriptions");
        
        if (empty($pushSubscriptions)) {
            error_log("DEBUG: No push subscriptions found - exiting");
            $this->logger?->info('No push subscriptions found');
            return;
        }

        $message = json_encode([
            'title' => 'Sound Level Alert!',
            'body' => sprintf(
                "Sound level on sensor %s is %d dB", 
                $sensorReading->getSensor()->getHwid(), 
                $sensorReading->getLevel()
            ),
            'icon' => '/icon-192x192.png',
            'data' => [
                'sensorId' => $sensorReading->getSensor()->getId(),
                'level' => $sensorReading->getLevel(),
                'timestamp' => $sensorReading->getReadedAt()->format('c'),
            ]
        ]);

        $notifications = [];
        
        foreach ($pushSubscriptions as $sub) {
            try {
                error_log("DEBUG: Creating subscription for endpoint: " . substr($sub->getEndpoint(), 0, 50) . "...");
                $subscription = Subscription::create([
                    'endpoint' => $sub->getEndpoint(),
                    'publicKey' => $sub->getP256dh(),
                    'authToken' => $sub->getAuth(),
                ]);
                
                $notifications[] = [
                    'subscription' => $subscription,
                    'payload' => $message,
                ];
                error_log("DEBUG: Successfully created subscription for ID: " . $sub->getId());
            } catch (\Exception $e) {
                error_log("DEBUG: Failed to create subscription: " . $e->getMessage());
                $this->logger?->error('Failed to create subscription', [
                    'error' => $e->getMessage(),
                    'subscription_id' => $sub->getId(),
                ]);
            }
        }

        // Send notifications in batch
        error_log("DEBUG: Preparing to queue " . count($notifications) . " notifications");
        foreach ($notifications as $notification) {
            $this->webPush->queueNotification(
                $notification['subscription'],
                $notification['payload']
            );
        }

        // Process the queue and handle results
        error_log("DEBUG: Starting to flush WebPush queue...");
        foreach ($this->webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            
            if ($report->isSuccess()) {
                error_log("DEBUG: ✅ Push notification sent successfully to: " . substr($endpoint, 0, 50) . "...");
                $this->logger?->info('Push notification sent successfully', [
                    'endpoint' => $endpoint,
                ]);
            } else {
                error_log("DEBUG: ❌ Failed to send push notification to: " . substr($endpoint, 0, 50) . "... Reason: " . $report->getReason());
                $this->logger?->error('Failed to send push notification', [
                    'endpoint' => $endpoint,
                    'reason' => $report->getReason(),
                ]);
                
                // If subscription is invalid, you might want to remove it
                if ($report->isSubscriptionExpired()) {
                    error_log("DEBUG: ⚠️ Subscription expired for: " . substr($endpoint, 0, 50) . "...");
                    $this->logger?->warning('Subscription expired, should be removed', [
                        'endpoint' => $endpoint,
                    ]);
                    // TODO: Remove expired subscription from database
                }
            }
        }
    }
}
