<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SemaphoreSmsService
{
    protected $apiKey;
    protected $client;
    protected $baseUrl = 'https://api.semaphore.co';

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
            $response = $this->client->post('/api/v4/messages', [
                'form_params' => [
                    'apikey' => $this->apiKey,
                    'number' => $number,
                    'message' => $message,
                    'sendername' => $senderId ?? config('app.name'),
                ]
            ]);

            $result = json_decode($response->getBody(), true);
            
            if (isset($result[0]['status']) && $result[0]['status'] === 'Queued') {
                Log::info('SMS sent successfully', ['response' => $result]);
                return $result;
            }
            
            Log::warning('SMS sending issue', ['response' => $result]);
            return $result;

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
            $response = $this->client->get('/api/v4/account', [
                'query' => ['apikey' => $this->apiKey],
                'http_errors' => false
            ]);

            $statusCode = $response->getStatusCode();
            $result = json_decode($response->getBody(), true);
            
            // Handle different HTTP status codes
            switch ($statusCode) {
                case 200:
                    return $result;
                    
                case 429:
                    Log::warning('Semaphore API Rate Limit Hit');
                    return ['error' => 'Rate limit exceeded. Please wait a few seconds and try again.', 'status_code' => 429];
                    
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
