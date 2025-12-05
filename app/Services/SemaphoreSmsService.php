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

            $form = [
                'apikey' => $this->apiKey,
                'number' => $normalized,
                'message' => $message,
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
            
            // Treat successful provider acceptance as success
            $statusField = $result[0]['status'] ?? null;
            $hasMessageId = isset($result[0]['message_id']);
            if ($statusCode === 200 && ($statusField === 'Queued' || $statusField === 'Pending' || $statusField === 'Sent' || $hasMessageId)) {
                Log::info('SMS accepted by provider', ['status' => $statusField, 'response' => $result]);
                return $result;
            }
            
            // Extract a meaningful error message
            $errorMessage = 'Failed to send SMS.';
            if (is_array($result)) {
                // Common Semaphore validation/error patterns
                if (isset($result[0]['status']) && $result[0]['status'] === 'Failed' && isset($result[0]['message'])) {
                    $errorMessage = $result[0]['message'];
                } elseif (isset($result[0]['error'])) {
                    $errorMessage = $result[0]['error'];
                } elseif (isset($result['error'])) {
                    $errorMessage = is_string($result['error']) ? $result['error'] : json_encode($result['error']);
                } elseif (isset($result[0]['status']) && isset($result[0]['status_message'])) {
                    $errorMessage = $result[0]['status_message'];
                } elseif (isset($result['sendername'])) {
                    // e.g. {"sendername":["The selected sendername is invalid."]}
                    $err = $result['sendername'];
                    if (is_array($err)) {
                        $errorMessage = 'Invalid sender name: ' . implode(', ', $err);
                    } else {
                        $errorMessage = 'Invalid sender name: ' . $err;
                    }
                } elseif (isset($result['number'])) {
                    // e.g. {"number":["The number format is invalid."]}
                    $err = $result['number'];
                    if (is_array($err)) {
                        $errorMessage = 'Invalid number: ' . implode(', ', $err);
                    } else {
                        $errorMessage = 'Invalid number: ' . $err;
                    }
                } elseif (isset($result['message'])) {
                    // e.g. {"message":["The message may not be greater than 160 characters."]}
                    $err = $result['message'];
                    if (is_array($err)) {
                        $errorMessage = implode(', ', $err);
                    } else {
                        $errorMessage = (string)$err;
                    }
                }
            }

            Log::warning('SMS sending issue', ['status' => $statusCode, 'response' => $result, 'error' => $errorMessage]);
            return ['error' => $errorMessage, 'status_code' => $statusCode, 'raw' => $result];

        } catch (\Exception $e) {
            Log::error('Semaphore SMS Error: ' . $e->getMessage());
            return false;
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
}
