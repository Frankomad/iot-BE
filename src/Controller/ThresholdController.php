<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ThresholdDTO;
use App\Entity\Threshold;
use App\Enum\ThresholdType;
use App\Repository\ThresholdRepository;
use App\Service\ThresholdService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/v1/threshold')]
final class ThresholdController extends AbstractController
{
    public function __construct(
        private ThresholdRepository    $thresholdRepository,
        private ThresholdService     $thresholdService,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/all')]
    public function getAllThresholdsAction(): JsonResponse
    {
       return $this->json($this->thresholdRepository->findAll());
    }

    #[Route(methods: ['POST'])]
    public function editThresholdsAction(#[MapRequestPayload] ThresholdDTO $thresholdDTO): JsonResponse
    {
        $thresholdType = ThresholdType::from($thresholdDTO->type);
        $threshold = $this->thresholdRepository->findOneBy(['type' => $thresholdType]);

        if (!$threshold) {
            throw $this->createNotFoundException();
        }

        $threshold->setLevel($thresholdDTO->level);
        $this->entityManager->flush();

        $this->thresholdService->sendToHomeAssistant($thresholdDTO);

        return $this->json($threshold);
    }
}
