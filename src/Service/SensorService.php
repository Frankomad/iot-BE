<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\SensorDTO;
use App\Entity\Sensor;
use App\Entity\Threshold;
use App\Enum\ThresholdType;
use Doctrine\ORM\EntityManagerInterface;

final class SensorService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function createSensor(SensorDTO $sensorDTO): Sensor
    {
        $sensor = new Sensor();
        $sensor->setHwid($sensorDTO->hwid);
        $sensor->setLocation($sensorDTO->location);
        $this->entityManager->persist($sensor);

        $lowThreshold = new Threshold();
        $lowThreshold->setLevel(Threshold::LOW_THRESHOLD_DEFAULT);
        $lowThreshold->setType(ThresholdType::LOW);
        $lowThreshold->setSensor($sensor);
        $this->entityManager->persist($lowThreshold);

        $mediumThreshold = new Threshold();
        $mediumThreshold->setLevel(Threshold::MEDIUM_THRESHOLD_DEFAULT);
        $mediumThreshold->setType(ThresholdType::MEDIUM);
        $mediumThreshold->setSensor($sensor);
        $this->entityManager->persist($mediumThreshold);

        $highThreshold = new Threshold();
        $highThreshold->setLevel(Threshold::HIGH_THRESHOLD_DEFAULT);
        $highThreshold->setType(ThresholdType::HIGH);
        $highThreshold->setSensor($sensor);
        $this->entityManager->persist($highThreshold);

        $this->entityManager->flush();

        return $sensor;
    }
}
