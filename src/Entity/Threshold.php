<?php

namespace App\Entity;

use App\Enum\ThresholdType;
use App\Repository\ThresholdRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThresholdRepository::class)]
class Threshold
{
    public const int LOW_THRESHOLD_DEFAULT = 10;
    public const int MEDIUM_THRESHOLD_DEFAULT = 20;
    public const int HIGH_THRESHOLD_DEFAULT = 60;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ThresholdType $type;
    #[ORM\Column]
    private int $level;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sensor $sensor = null;

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    public function getType(): ThresholdType
    {
        return $this->type;
    }

    public function setType(ThresholdType $type): void
    {
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSensor(): ?Sensor
    {
        return $this->sensor;
    }

    public function setSensor(?Sensor $sensor): static
    {
        $this->sensor = $sensor;

        return $this;
    }
}
