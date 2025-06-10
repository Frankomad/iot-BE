<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\SensorReading;
use App\Repository\PushSubscriptionRepository;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

final class NotificationService
{
    private $webPush;

    public function __construct(
        private PushSubscriptionRepository $pushSubscriptionRepository,
    )
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => 'https://github.com/Minishlink/web-push-php-example/',
                'publicKey' => 'BBgCxMD9IekBKEXl9Kb7orXDCS7mBZ2Q1uu__f1qj1ksfGouYjbvB87yod_Jhl7nNygrg3vfCaPGJBr8QNO9HNk',
                'privateKey' => 'VK37Xs6acytgvpv7mxt85Mp8VB5TdURA6IWR4iAzaJM',
            ],
        ]);
    }

    public function sendNotifications(SensorReading $sensorReading)
    {
        $pushSubscription = $this->pushSubscriptionRepository->findAll();
        $message = sprintf("Sound level measured on sensor with ID: %s is %ddB", $sensorReading->getSensor()->getId(), $sensorReading->getLevel());

        foreach ($pushSubscription as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->getEndpoint(),
                'publicKey' => $sub->getP256dh(),
                'authToken' => $sub->getAuth(),
            ]);

            $this->webPush->sendOneNotification($subscription, $message);
        }
    }
}
