<?php

namespace App\Entity;

use App\Enum\ThresholdType;
use App\Repository\ThresholdRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThresholdRepository::class)]
class Threshold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column]
    private ThresholdType $type;
    #[ORM\Column]
    private int $level;

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
}
