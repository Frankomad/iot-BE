<?php

declare(strict_types=1);

namespace App\DTO;

class SensorDTO
{
    public function __construct(
        public string $hwid,
    )
    {
    }
}
