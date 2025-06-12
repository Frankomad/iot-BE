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

        $highThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::HIGH, 'sensor' => $sensor]);
        if (null === $highThreshold) {
            throw new \LogicException('High threshold not found');
        }

        // DEBUG: Log threshold comparison
        error_log("DEBUG: Sensor reading level: " . $sensorReading->getLevel() . " dB");
        error_log("DEBUG: High threshold level: " . $highThreshold->getLevel() . " dB");
        error_log("DEBUG: Should trigger notification: " . ($sensorReading->getLevel() > $highThreshold->getLevel() ? 'YES' : 'NO'));

        if ($sensorReading->getLevel() > $highThreshold->getLevel()) {
            error_log("DEBUG: Calling notification service for sensor: " . $sensorReading->getSensor()->getHwid());
            $this->notificationService->sendNotifications($sensorReading);
            error_log("DEBUG: Notification service call completed");
        }

        return $sensorReading;
    }

    public function saveSensorReading(SensorReading $sensorReading): void
    {
        // DEBUG: Log when a new reading is being saved
        error_log("DEBUG: ⚡ NEW SENSOR READING BEING SAVED ⚡");
        error_log("DEBUG: Sensor: " . $sensorReading->getSensor()->getHwid());
        error_log("DEBUG: Level: " . $sensorReading->getLevel() . " dB");
        error_log("DEBUG: Time: " . $sensorReading->getReadedAt()->format('Y-m-d H:i:s'));

        $this->entityManager->persist($sensorReading);
        $this->entityManager->flush();

        // FIX: Use the high threshold for the specific sensor
        $highThreshold = $this->thresholdRepository->findOneBy([
            'type' => ThresholdType::HIGH,
            'sensor' => $sensorReading->getSensor()
        ]);
        if (null === $highThreshold) {
            error_log("DEBUG: ❌ HIGH THRESHOLD NOT FOUND for sensor: " . $sensorReading->getSensor()->getHwid());
            throw new \LogicException('High threshold not found');
        }

        // DEBUG: Log threshold comparison
        error_log("DEBUG: Sensor reading level: " . $sensorReading->getLevel() . " dB");
        error_log("DEBUG: High threshold level: " . $highThreshold->getLevel() . " dB");
        error_log("DEBUG: Should trigger notification: " . ($sensorReading->getLevel() > $highThreshold->getLevel() ? 'YES' : 'NO'));

        if ($sensorReading->getLevel() > $highThreshold->getLevel()) {
            error_log("DEBUG: 🚨 TRIGGERING NOTIFICATION! 🚨");
            error_log("DEBUG: Calling notification service for sensor: " . $sensorReading->getSensor()->getHwid());
            $this->notificationService->sendNotifications($sensorReading);
            error_log("DEBUG: Notification service call completed");
        } else {
            error_log("DEBUG: ✅ No notification needed - level below threshold");
        }
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
