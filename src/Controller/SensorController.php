<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\SensorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/sensor')]
final class SensorController extends AbstractController
{
    public function __construct(
        private SensorRepository $sensorRepository,
    )
    {
    }

    #[Route('/all')]
    public function getAllSensorsAction(): JsonResponse
    {
       return $this->json($this->sensorRepository->findAll());
    }
}
