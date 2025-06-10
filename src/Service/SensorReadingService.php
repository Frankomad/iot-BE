<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\SensorReadingDTO;
use App\Entity\Sensor;
use App\Entity\SensorReading;
use App\Entity\Threshold;
use App\Enum\ThresholdType;
use App\Repository\SensorReadingRepository;
use App\Repository\SensorRepository;
use App\Repository\ThresholdRepository;
use Doctrine\ORM\EntityManagerInterface;

final class SensorReadingService
{
    public function __construct(
        private SensorReadingRepository $sensorReadingRepository,
        private SensorRepository $sensorRepository,
        private ThresholdRepository $thresholdRepository,
        private NotificationService $notificationService,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function createSensorReading(SensorReadingDTO $sensorReadingDTO): SensorReading
    {
        $sensor = $this->sensorRepository->findOneBy(['hwid' => $sensorReadingDTO->sensorHwid]);

        if (null === $sensor) {
            throw new \LogicException('Sensor not found');
        }

        $sensorReading = new SensorReading();
        $sensorReading->setSensor($sensor);
        $sensorReading->setLevel($sensorReadingDTO->level);
        $readedAt = (new \DateTimeImmutable())->setTimestamp($sensorReadingDTO->timestamp);
        $sensorReading->setReadedAt($readedAt);

        $this->entityManager->persist($sensorReading);
        $this->entityManager->flush();

        $highThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::HIGH]);
        if (null === $highThreshold) {
            throw new \LogicException('High threshold not found');
        }

        if ($sensorReading->getLevel() > $highThreshold->getLevel()) {
            $this->notificationService->sendNotifications($sensorReading);
        }

        return $sensorReading;
    }

    public function getLastReading(Sensor $sensor): ?SensorReading
    {
        return $this->sensorReadingRepository->getLastReading($sensor);
    }

    public function getAverageReadingLevelInLastHours(Sensor $sensor, int $inLastSeconds): float
    {
        $dateTime = new \DateTime("-{$inLastSeconds} seconds");

        $readings = $this->sensorReadingRepository->getReadingsSinceTime($sensor, $dateTime);

        return count($readings) ?
            array_sum(array_map(static fn(SensorReading $reading): int => $reading->getLevel(), $readings))/count($readings)
            : 0;
    }

}
