<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SemaphoreSmsService
{
    protected $apiKey;
    protected $client;
    protected $baseUrl = 'https://api.semaphore.co';
    protected $balanceTtlSeconds = 60; // cache TTL to avoid 429

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }

    /**
     * Send SMS
     * 
     * @param string $number 09762129986
     * @param string $message HIIIIIII, it's Raymond
     * @param string|null $senderId SMAC
     * @return array|false
     */
    public function sendSms($number, $message, $senderId = null)
    {
        try {
            // Normalize number to PH E.164 where applicable
            $normalized = $this->normalizePhoneNumber($number);
            if (!$normalized) {
                Log::warning('Invalid phone number provided', ['number' => $number]);
                return [
                    'error' => 'Invalid phone number format. Use 09XXXXXXXXX or +639XXXXXXXXX.',
                ];
            }

            // Validate sender name: Semaphore requires up to 11 alphanumeric chars
            $sender = $senderId ?? (config('services.semaphore.sender_name') ?? config('app.name'));
            if (is_string($sender)) {
                // Remove non-alphanumeric and trim length to 11
                $sanitized = preg_replace('/[^A-Za-z0-9]/', '', $sender) ?: $sender;
                if (strlen($sanitized) > 11) {
                    $sanitized = substr($sanitized, 0, 11);
                }
            } else {
                $sanitized = null;
            }

            // Split messages into 160-char parts and send sequentially
            $segments = [];
            $len = mb_strlen($message);
            for ($i = 0; $i < $len; $i += 160) {
                $segments[] = mb_substr($message, $i, 160);
            }
            if (empty($segments)) {
                $segments = [''];
            }

            $allResults = [];
            foreach ($segments as $idx => $seg) {
                $form = [
                    'apikey' => $this->apiKey,
                    'number' => $normalized,
                    'message' => $seg,
                ];
                if (!empty($sanitized)) {
                    $form['sendername'] = $sanitized;
                }

                $response = $this->client->post('/api/v4/messages', [
                    'form_params' => $form,
                    'http_errors' => false
                ]);

                $statusCode = $response->getStatusCode();
                $result = json_decode($response->getBody(), true);
                $allResults[] = $result;

                $statusField = $result[0]['status'] ?? null;
                $hasMessageId = isset($result[0]['message_id']);
                if ($statusCode === 200 && ($statusField === 'Queued' || $statusField === 'Pending' || $statusField === 'Sent' || $hasMessageId)) {
                    Log::info('SMS segment accepted by provider', ['segment' => $idx + 1, 'status' => $statusField, 'response' => $result]);
                } else {
                    $errorMessage = $this->extractErrorMessage($result) ?? 'Failed to send SMS.';
                    Log::warning('SMS sending issue', ['segment' => $idx + 1, 'status' => $statusCode, 'response' => $result, 'error' => $errorMessage]);
                }
            }

            return $allResults;

        } catch (\Exception $e) {
            Log::error('Semaphore SMS Error: ' . $e->getMessage());
            return ['error' => 'SMS provider error: ' . $e->getMessage(), 'status_code' => 0];
        }
    }

    /**
     * Check account balance
     * 
     * @return array|false
     */
    public function getBalance()
    {
        try {
            // Cache balance to reduce rate limit hits
            $cacheKey = 'semaphore_balance_cache';
            if (function_exists('cache')) {
                $cached = cache()->get($cacheKey);
                if ($cached) {
                    return $cached;
                }
            }

            $response = $this->client->get('/api/v4/account', [
                'query' => ['apikey' => $this->apiKey],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $result = json_decode($response->getBody(), true);
            
            // Handle different HTTP status codes
            switch ($statusCode) {
                case 200:
                    // Store in cache
                    if (function_exists('cache')) {
                        cache()->put($cacheKey, $result, $this->balanceTtlSeconds);
                    }
                    return $result;
                    
                case 429:
                    Log::warning('Semaphore API Rate Limit Hit');
                    return ['error' => 'Rate limit exceeded. Please wait 60 seconds and try again.', 'status_code' => 429];
                    
                case 401:
                case 403:
                    Log::error('Semaphore API Authentication Failed', ['response' => $result]);
                    return ['error' => 'Invalid API Key. Please check your SEMAPHORE_API_KEY in .env file.', 'status_code' => $statusCode];
                    
                default:
                    Log::error('Semaphore Balance API Error', [
                        'status' => $statusCode,
                        'response' => $result
                    ]);
                    return ['error' => $result ?: 'API request failed', 'status_code' => $statusCode];
            }
        } catch (\Exception $e) {
            Log::error('Semaphore Balance Check Error: ' . $e->getMessage());
            return ['error' => $e->getMessage(), 'status_code' => 0];
        }
    }

    /**
     * Normalize PH mobile numbers to E.164 (+639XXXXXXXXX)
     * Accepts 09XXXXXXXXX, 9XXXXXXXXX, +639XXXXXXXXX, 639XXXXXXXXX
     * Returns null if invalid
     */
    private function normalizePhoneNumber(?string $number): ?string
    {
        if (!$number) return null;
        $digits = preg_replace('/\D+/', '', $number);

        // Handle leading 0 (e.g., 09XXXXXXXXX -> +639XXXXXXXXX)
        if (preg_match('/^09(\d{9})$/', $digits, $m)) {
            return '+639' . $m[1];
        }
        // Handle without leading 0 (e.g., 9XXXXXXXXX -> +639XXXXXXXXX)
        if (preg_match('/^9(\d{9})$/', $digits, $m)) {
            return '+639' . $m[1];
        }
        // Handle 639XXXXXXXXX -> +639XXXXXXXXX
        if (preg_match('/^639(\d{9})$/', $digits, $m)) {
            return '+639' . $m[1];
        }
        // Handle already E.164 +639XXXXXXXXX
        if (preg_match('/^\+?639(\d{9})$/', $number)) {
            return strpos($number, '+') === 0 ? $number : ('+' . $number);
        }
        // Allow other E.164 numbers if they look valid (e.g., +1XXXXXXXXXX)
        if (preg_match('/^\+\d{10,15}$/', $number)) {
            return $number;
        }
        return null;
    }

    /**
     * Get account transactions
     * 
     * @param int $limit Number of transactions (default: 100, max: 1000)
     * @param int $page Page number
     * @return array|false
     */
    public function getTransactions($limit = 100, $page = 1)
    {
        try {
            $response = $this->client->get('/api/v4/account/transactions', [
                'query' => [
                    'apikey' => $this->apiKey,
                    'limit' => $limit,
                    'page' => $page
                ]
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('Semaphore Transactions Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS to multiple numbers
     * 
     * @param array $numbers Array of phone numbers
     * @param string $message Message content
     * @param string|null $senderId Sender name
     * @return array Results for each number
     */
    public function sendBulkSms(array $numbers, $message, $senderId = null)
    {
        $results = [];
        
        foreach ($numbers as $number) {
            $results[$number] = $this->sendSms($number, $message, $senderId);
        }
        
        return $results;
    }

    private function extractErrorMessage($result): ?string
    {
        if (!is_array($result)) return null;
        if (isset($result[0]['status']) && $result[0]['status'] === 'Failed' && isset($result[0]['message'])) {
            return $result[0]['message'];
        }
        if (isset($result[0]['error'])) {
            return $result[0]['error'];
        }
        if (isset($result['error'])) {
            return is_string($result['error']) ? $result['error'] : json_encode($result['error']);
        }
        if (isset($result[0]['status']) && isset($result[0]['status_message'])) {
            return $result[0]['status_message'];
        }
        if (isset($result['sendername'])) {
            $err = $result['sendername'];
            return is_array($err) ? ('Invalid sender name: ' . implode(', ', $err)) : ('Invalid sender name: ' . $err);
        }
        if (isset($result['number'])) {
            $err = $result['number'];
            return is_array($err) ? ('Invalid number: ' . implode(', ', $err)) : ('Invalid number: ' . $err);
        }
        if (isset($result['message'])) {
            $err = $result['message'];
            return is_array($err) ? implode(', ', $err) : (string)$err;
        }
        return null;
    }
}
