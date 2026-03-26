<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\DomainCheck;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class DomainCheckService
{
    /**
     * Check a single domain and persist the result.
     */
    public function check(Domain $domain): DomainCheck
    {
        $result = $this->performRequest($domain);

        return $domain->checks()->create($result);
    }

    /**
     * Make the HTTP request and measure response time.
     *
     * @return array{status: string, response_code: int|null, response_time: int|null, error_message: string|null}
     */
    private function performRequest(Domain $domain): array
    {
        $url = 'https://' . $domain->name;
        $startedAt = hrtime(true);

        try {
            $response = Http::timeout($domain->timeout)
                ->withOptions([
                    'allow_redirects' => ['max' => 5, 'strict' => false],
                    'verify'          => true,
                ])
                ->send($domain->method, $url);

            $responseTime = $this->elapsedMs($startedAt);
            $code = $response->status();

            // Treat 2xx / 3xx as online; 4xx / 5xx as offline
            $isOnline = $code < 400;

            return [
                'status'        => $isOnline ? 'online' : 'offline',
                'response_code' => $code,
                'response_time' => $isOnline ? $responseTime : null,
                'error_message' => $isOnline ? null : "HTTP {$code}",
            ];
        } catch (ConnectionException $e) {
            return $this->errorResult($e->getMessage());
        } catch (\Exception $e) {
            return $this->errorResult($this->sanitizeMessage($e->getMessage()));
        }
    }

    private function elapsedMs(int $startNs): int
    {
        return (int) round((hrtime(true) - $startNs) / 1_000_000);
    }

    /**
     * @return array{status: string, response_code: null, response_time: null, error_message: string}
     */
    private function errorResult(string $message): array
    {
        return [
            'status'        => 'offline',
            'response_code' => null,
            'response_time' => null,
            'error_message' => mb_substr($message, 0, 500),
        ];
    }

    /**
     * Strip cURL noise like "cURL error 6: " from exception messages.
     */
    private function sanitizeMessage(string $message): string
    {
        return preg_replace('/^cURL error \d+:\s*/i', '', $message) ?? $message;
    }
}
