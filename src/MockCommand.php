<?php

declare(strict_types=1);

namespace App;

use App\Entity\Sensor;
use App\Entity\SensorReading;
use App\Entity\Threshold;
use App\Enum\ThresholdType;
use App\Repository\SensorRepository;
use App\Repository\ThresholdRepository;
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
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createSensors();
        $this->createThresholds();

        $this->entityManager->flush();

        $sensors = $this->sensorRepository->findAll();

        while (true) {
            $sensor = $sensors[random_int(0,2)];

            $reading = new SensorReading();
            $reading->setReadedAt(new \DateTimeImmutable());
            $reading->setLevel(random_int(1, 100));
            $reading->setSensor($sensor);
            $this->entityManager->persist($reading);
            $this->entityManager->flush();

            sleep(5);
        }
    }

    private function createThresholds(): void
    {
        $lowThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::LOW]);
        if ($lowThreshold === null) {
            $lowThreshold = new Threshold();
            $lowThreshold->setType(ThresholdType::LOW);
            $lowThreshold->setLevel(10);
            $this->entityManager->persist($lowThreshold);
        }

        $mediumThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::MEDIUM]);
        if ($mediumThreshold === null) {
            $mediumThreshold = new Threshold();
            $mediumThreshold->setType(ThresholdType::MEDIUM);
            $mediumThreshold->setLevel(20);
            $this->entityManager->persist($mediumThreshold);
        }

        $highThreshold = $this->thresholdRepository->findOneBy(['type' => ThresholdType::HIGH]);
        if ($highThreshold === null) {
            $highThreshold = new Threshold();
            $highThreshold->setType(ThresholdType::HIGH);
            $highThreshold->setLevel(60);
            $this->entityManager->persist($highThreshold);
        }
    }

    private function createSensors(): void
    {
        $sensor1 = $this->sensorRepository->find(1);
        if (!$sensor1) {
            $sensor1 = new Sensor();
            $sensor1->setHwid('abc');
            $this->entityManager->persist($sensor1);
        }

        $sensor2 = $this->sensorRepository->find(2);
        if (!$sensor2) {
            $sensor2 = new Sensor();
            $sensor2->setHwid('def');
            $this->entityManager->persist($sensor2);
        }

        $sensor3 = $this->sensorRepository->find(3);
        if (!$sensor3) {
            $sensor3 = new Sensor();
            $sensor3->setHwid('ghi');
            $this->entityManager->persist($sensor3);
        }
    }
}
