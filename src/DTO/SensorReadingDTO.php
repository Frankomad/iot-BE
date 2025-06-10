<?php

declare(strict_types=1);

namespace App\DTO;

class SensorReadingDTO
{
    public function __construct(
        public string $sensorHwid,
        public int $level,
        public int $timestamp,
    )
    {
    }
}
