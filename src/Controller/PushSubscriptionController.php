<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\PushSubscriptionDTO;
use App\Entity\PushSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/v1/push/subscribe')]
final class PushSubscriptionController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route(methods: ['POST'])]
    public function pushSubscribeAction(#[MapRequestPayload] PushSubscriptionDTO $pushSubscriptionDTO): JsonResponse
    {
        $pushSubscription = new PushSubscription();
        $pushSubscription->setEndpoint($pushSubscriptionDTO->endpoint);
        $pushSubscription->setP256dh($pushSubscriptionDTO->p256dh);
        $pushSubscription->setAuth($pushSubscriptionDTO->auth);
        $this->entityManager->persist($pushSubscription);
        $this->entityManager->flush();

        return $this->json($pushSubscription);
    }
}
