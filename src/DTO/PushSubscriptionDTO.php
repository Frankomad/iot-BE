<?php

namespace App\DTO;

class PushSubscriptionDTO
{
    public function __construct(
        public string $endpoint,
        public string $p256dh,
        public string $auth,
    )
    {
    }
}
