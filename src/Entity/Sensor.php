<?php

namespace App\Entity;

use App\Repository\SensorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: SensorRepository::class)]
class Sensor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, SensorReading>
     */
    #[ORM\OneToMany(targetEntity: SensorReading::class, mappedBy: 'sensor', orphanRemoval: true)]
    #[Ignore]
    private Collection $sensorReadings;

    #[ORM\Column(length: 255)]
    private ?string $hwid = null;

    public function __construct()
    {
        $this->sensorReadings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, SensorReading>
     */
    public function getSensorReadings(): Collection
    {
        return $this->sensorReadings;
    }

    public function addSensorReading(SensorReading $sensorReading): static
    {
        if (!$this->sensorReadings->contains($sensorReading)) {
            $this->sensorReadings->add($sensorReading);
            $sensorReading->setSensor($this);
        }

        return $this;
    }

    public function removeSensorReading(SensorReading $sensorReading): static
    {
        if ($this->sensorReadings->removeElement($sensorReading)) {
            // set the owning side to null (unless already changed)
            if ($sensorReading->getSensor() === $this) {
                $sensorReading->setSensor(null);
            }
        }

        return $this;
    }

    public function getHwid(): ?string
    {
        return $this->hwid;
    }

    public function setHwid(string $hwid): static
    {
        $this->hwid = $hwid;

        return $this;
    }
}
