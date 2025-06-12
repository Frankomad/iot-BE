<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ThresholdDTO;
use App\Entity\Sensor;
use App\Entity\Threshold;
use App\Enum\ThresholdType;
use App\Repository\SensorRepository;
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
        private SensorRepository     $sensorRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/{sensor}')]
    public function getAllThresholdsForSensorAction(Sensor $sensor): JsonResponse
    {
       return $this->json($this->thresholdRepository->findBy(['sensor' => $sensor]));
    }

    #[Route(methods: ['POST'])]
    public function editThresholdsAction(#[MapRequestPayload] ThresholdDTO $thresholdDTO): JsonResponse
    {
        $sensor = $this->sensorRepository->findOneBy(['hwid' => $thresholdDTO->sensorHwid]);
        if (!$sensor) {
            throw $this->createNotFoundException();
        }

        $thresholdType = ThresholdType::from($thresholdDTO->type);
        $threshold = $this->thresholdRepository->findOneBy(['type' => $thresholdType, 'sensor' => $sensor]);

        if (!$threshold) {
            throw $this->createNotFoundException();
        }

        $threshold->setLevel($thresholdDTO->level);

        //$this->thresholdService->sendToHomeAssistant($thresholdDTO);

        $this->entityManager->flush();

        return $this->json($threshold);
    }
}
