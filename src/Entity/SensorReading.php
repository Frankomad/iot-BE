<?php

namespace App\Entity;

use App\Repository\SensorReadingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SensorReadingRepository::class)]
class SensorReading
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $level = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $readedAt = null;

    #[ORM\ManyToOne(inversedBy: 'sensorReadings')]
    #[ORM\JoinColumn(nullable: false)]
    private Sensor $sensor;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getReadedAt(): ?\DateTimeImmutable
    {
        return $this->readedAt;
    }

    public function setReadedAt(\DateTimeImmutable $readedAt): static
    {
        $this->readedAt = $readedAt;

        return $this;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): static
    {
        $this->sensor = $sensor;

        return $this;
    }
}
