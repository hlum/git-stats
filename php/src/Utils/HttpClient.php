<?php

declare(strict_types=1);

namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpClient
{
    private const GITHUB_API_URL = 'https://api.github.com/graphql';
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function request(array $data, array $headers): array
    {
        $response = $this->client->post(self::GITHUB_API_URL, [
            'headers' => array_merge([
                'Content-Type' => 'application/json',
            ], $headers),
            'json' => $data,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
