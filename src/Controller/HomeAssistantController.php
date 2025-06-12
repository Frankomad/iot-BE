<?php

namespace App\Controller;

use App\DTO\SensorDTO;
use App\DTO\ThresholdDTO;
use App\DTO\SensorReadingDTO;
use App\Entity\Sensor;
use App\Entity\SensorReading;
use App\Service\SensorReadingService;
use App\Service\SensorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/home-assistant')]
final class HomeAssistantController extends AbstractController
{

    public function __construct(
        private readonly SensorReadingService $sensorReadingService,
        private readonly SensorService $sensorService,
    )
    {
    }

    #[Route(path:'/sensor' ,methods: ['POST'])]
    public function createSensorAction(#[MapRequestPayload] SensorDTO $sensorDTO): JsonResponse
    {
        return $this->json($this->sensorService->createSensor($sensorDTO));
    }

    #[Route(path:'/sensorReading' ,methods: ['POST'])]
    public function createSensorReadingAction(#[MapRequestPayload] SensorReadingDTO $sensorReadingDTO): JsonResponse
    {
        return $this->json($this->sensorReadingService->createSensorReading($sensorReadingDTO));
    }
}
