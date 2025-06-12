<?php

declare(strict_types=1);

namespace App\DTO;

class ThresholdDTO
{
    public function __construct(
        public string $type,
        public string $sensorHwid,
        public int $level,
    )
    {
    }
}
