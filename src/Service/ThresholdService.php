<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ThresholdDTO;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ThresholdService
{
    public const string HOME_ASSISTANT_ENDPOINT = 'home-assistant-ip-address';

    public function __construct(
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function sendToHomeAssistant(ThresholdDTO $thresholdDTO): void
    {
        $this->httpClient->request('POST', self::HOME_ASSISTANT_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $thresholdDTO,
        ]);
    }
}
