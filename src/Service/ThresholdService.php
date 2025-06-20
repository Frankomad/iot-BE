<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\ThresholdDTO;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ThresholdService
{
    // Set this endpoint to your custom Home Assistant endpoint
    public const HOME_ASSISTANT_ENDPOINT = 'http://192.168.239.50:1880/endpoint/threshold';

    public function __construct(
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
    )
    {
    }

    public function sendToHomeAssistant(ThresholdDTO $thresholdDTO): void
    {

        $json = $this->serializer->serialize($thresholdDTO, 'json');

        $response = $this->httpClient->request('POST', self::HOME_ASSISTANT_ENDPOINT, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => $json,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to send home assistant IP address');
        }
    }
}
