<?php

namespace App\Controller;

use App\DTO\SensorDTO;
use App\DTO\ThresholdDTO;
use App\DTO\SensorReadingDTO;
use App\Entity\Sensor;
use App\Entity\SensorReading;
use App\Service\SensorReadingService;
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
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route(path:'/sensor' ,methods: ['POST'])]
    public function createSensorAction(#[MapRequestPayload] SensorDTO $sensorDTO): JsonResponse
    {
        $sensor = new Sensor();
        $sensor->setHwid($sensorDTO->hwid);
        $this->entityManager->persist($sensor);
        $this->entityManager->flush();
        return $this->json($sensor);
    }

    #[Route(path:'/sensorReading' ,methods: ['POST'])]
    public function createSensorReadingAction(#[MapRequestPayload] SensorReadingDTO $sensorReadingDTO): JsonResponse
    {
        return $this->json($this->sensorReadingService->createSensorReading($sensorReadingDTO));
    }
}
