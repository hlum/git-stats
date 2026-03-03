<?php

declare(strict_types=1);

namespace App\Utils;

use Exception;

class Retryer
{
    /**
     * @template T
     * @param callable(): T $fetcher
     * @param int $retries
     * @return T
     * @throws Exception
     */
    public static function retry(callable $fetcher, int $retries = 3): mixed
    {
        $lastError = null;

        for ($i = 0; $i < $retries; $i++) {
            try {
                return $fetcher();
            } catch (Exception $e) {
                $lastError = $e;
                if ($i < $retries - 1) {
                    usleep(1000000 * ($i + 1)); // Sleep for (i+1) seconds
                }
            }
        }

        throw $lastError ?? new Exception('Unknown error during retry');
    }
}
