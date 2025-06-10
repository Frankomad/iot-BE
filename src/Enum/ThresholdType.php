<?php

namespace App\Enum;

enum ThresholdType: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
}
