<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Sensor;
use App\Repository\SensorReadingRepository;
use App\Service\SensorReadingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/sensorReading')]
class SensorReadingController extends AbstractController
{
    public function __construct(
        private SensorReadingService $sensorReadingService,
    )
    {
    }

    #[Route('/{sensor}')]
    public function getLastReadingForSensor(Sensor $sensor): JsonResponse
    {
        return $this->json($this->sensorReadingService->getLastReading($sensor));
    }

    #[Route('/{sensor}/average/{inLastSeconds}')]
    public function getAverageLevelInLastHoursAction(Sensor $sensor, int $inLastSeconds): JsonResponse
    {
        return $this->json(['average' => $this->sensorReadingService->getAverageReadingLevelInLastHours($sensor, $inLastSeconds)]);
    }
}
