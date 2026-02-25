<?php

declare(strict_types=1);

namespace App\Utils;

use RuntimeException;

class HttpClient
{
    private const GITHUB_API_URL = 'https://api.github.com/graphql';

    public function __construct()
    {
        // No dependencies needed - uses native PHP curl
    }

    /**
     * @throws RuntimeException
     */
    public function request(array $data, array $headers): array
    {
        $ch = curl_init(self::GITHUB_API_URL);
        
        if ($ch === false) {
            throw new RuntimeException('Failed to initialize curl');
        }

        $allHeaders = array_merge([
            'Content-Type: application/json',
            'User-Agent: PHP-GitStats/1.0',
        ], array_map(
            fn($key, $value) => "$key: $value",
            array_keys($headers),
            array_values($headers)
        ));

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $allHeaders,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException("Curl error: $error");
        }

        if ($httpCode >= 400) {
            throw new RuntimeException("HTTP error $httpCode: $response");
        }

        $decoded = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON response: ' . json_last_error_msg());
        }

        return $decoded;
    }
}
