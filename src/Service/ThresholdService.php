<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ThresholdDTO;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ThresholdService
{
    public const string HOME_ASSISTANT_ENDPOINT = '192.168.39.3/endpoint/threshold';

    public function __construct(
        private HttpClientInterface $httpClient,
    )
    {
    }

    public function sendToHomeAssistant(ThresholdDTO $thresholdDTO): void
    {
        $response = $this->httpClient->request('POST', self::HOME_ASSISTANT_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => $thresholdDTO,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to send home assistant IP address');
        }
    }
}
