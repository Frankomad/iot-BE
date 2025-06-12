<?php

declare(strict_types=1);

namespace App;

use App\Entity\Sensor;
use App\Entity\SensorReading;
use App\Entity\Threshold;
use App\Enum\ThresholdType;
use App\Repository\SensorRepository;
use App\Repository\ThresholdRepository;
use App\Service\SensorReadingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'mock:command')]
class MockCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SensorRepository $sensorRepository,
        private ThresholdRepository $thresholdRepository,
        private SensorReadingService $sensorReadingService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sensors = $this->createSensors();
        $this->createThresholds($sensors);

        $this->entityManager->flush();

        $sensors = $this->sensorRepository->findAll();

        while (true) {
            $sensor = $sensors[random_int(0,2)];

            $reading = new SensorReading();
            $reading->setReadedAt(new \DateTimeImmutable());
            $reading->setLevel(random_int(1, 100));
            $reading->setSensor($sensor);

            // Use the service to save the reading, which will trigger notifications
            $this->sensorReadingService->saveSensorReading($reading);

            sleep(5);
        }
    }

    /**
     * @param Sensor[] $sensors
     */
    private function createThresholds(array $sensors): void
    {
        foreach ($sensors as $sensor) {
            $lowThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::LOW, 'sensor' => $sensor]);
            if ($lowThreshold === null) {
                $lowThreshold = new Threshold();
                $lowThreshold->setType(ThresholdType::LOW);
                $lowThreshold->setLevel(Threshold::LOW_THRESHOLD_DEFAULT);
                $lowThreshold->setSensor($sensor);
                $this->entityManager->persist($lowThreshold);
            }

            $mediumThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::MEDIUM, 'sensor' => $sensor]);
            if ($mediumThreshold === null) {
                $mediumThreshold = new Threshold();
                $mediumThreshold->setType(ThresholdType::MEDIUM);
                $mediumThreshold->setLevel(Threshold::MEDIUM_THRESHOLD_DEFAULT);
                $mediumThreshold->setSensor($sensor);
                $this->entityManager->persist($mediumThreshold);
            }

            $highThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::HIGH, 'sensor' => $sensor]);
            if ($highThreshold === null) {
                $highThreshold = new Threshold();
                $highThreshold->setType(ThresholdType::HIGH);
                $highThreshold->setLevel(Threshold::HIGH_THRESHOLD_DEFAULT);
                $highThreshold->setSensor($sensor);
                $this->entityManager->persist($highThreshold);
            }
        }
    }

    private function createSensors(): array
    {
        $sensor1 = $this->sensorRepository->find(1);
        if (!$sensor1) {
            $sensor1 = new Sensor();
            $sensor1->setHwid('abc');
            $sensor1->setLocation('Zagreb');
            $this->entityManager->persist($sensor1);
        }

        $sensor2 = $this->sensorRepository->find(2);
        if (!$sensor2) {
            $sensor2 = new Sensor();
            $sensor2->setHwid('def');
            $sensor2->setLocation('Split');
            $this->entityManager->persist($sensor2);
        }

        $sensor3 = $this->sensorRepository->find(3);
        if (!$sensor3) {
            $sensor3 = new Sensor();
            $sensor3->setHwid('ghi');
            $sensor3->setLocation('Rijeka');
            $this->entityManager->persist($sensor3);
        }

        return [$sensor1, $sensor2, $sensor3];
    }
}
