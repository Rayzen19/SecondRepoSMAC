<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Guardian;
use App\Services\SemaphoreSmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    protected $smsService;

    public function __construct(SemaphoreSmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        return view('admin.sms.index');
    }

    public function sendTest(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $phone = $this->normalizePhone($request->phone);
        if (!$phone) {
            return back()->with('error', 'Invalid phone number format. Use 09XXXXXXXXX or +639XXXXXXXXX.');
        }

        $result = $this->smsService->sendSms(
            $phone,
            $request->message
        );

        // Interpret provider response more robustly
        $isSuccess = false;
        if (is_array($result)) {
            // Semaphore returns an array of message objects when successful
            $first = $result[0] ?? [];
            $status = $first['status'] ?? null;
            $hasId = isset($first['message_id']);
            $isSuccess = ($status === 'Queued' || $status === 'Pending' || $status === 'Sent' || $hasId) && !isset($result['error']);
        } elseif ($result === true) {
            $isSuccess = true;
        }

        if ($isSuccess) {
            return back()->with('success', 'Test SMS sent successfully! Check your phone.');
        }

        $errorMsg = is_array($result) && isset($result['error'])
            ? $result['error']
            : 'Failed to send SMS. Please check your number and API settings.';

        return back()->with('error', $errorMsg);
    }

    public function sendSingle(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:teacher,student,guardian',
            'recipient_id' => 'required|integer',
            'message' => 'required|string|max:160',
        ]);

        $recipient = $this->getRecipient($request->recipient_type, $request->recipient_id);

        if (!$recipient || !$recipient->phone) {
            return back()->with('error', 'Recipient phone number not found.');
        }

        $phone = $this->normalizePhone($recipient->phone);
        if (!$phone) {
            return back()->with('error', 'Recipient phone is invalid. Use 09XXXXXXXXX or +639XXXXXXXXX.');
        }

        $result = $this->smsService->sendSms(
            $phone,
            $request->message
        );

        // Interpret provider response more robustly
        $isSuccess = false;
        if (is_array($result)) {
            $first = $result[0] ?? [];
            $status = $first['status'] ?? null;
            $hasId = isset($first['message_id']);
            $isSuccess = ($status === 'Queued' || $status === 'Pending' || $status === 'Sent' || $hasId) && !isset($result['error']);
        } elseif ($result === true) {
            $isSuccess = true;
        }

        if ($isSuccess) {
            return back()->with('success', "SMS sent to {$recipient->first_name} {$recipient->last_name}");
        }

        $errorMsg = is_array($result) && isset($result['error'])
            ? $result['error']
            : 'Failed to send SMS.';
        return back()->with('error', $errorMsg);
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:teachers,students,guardians,custom',
            'message' => 'required|string|max:160',
            'phone_numbers' => 'required_if:recipient_type,custom|array',
            'phone_numbers.*' => 'string',
        ]);

        $phoneNumbers = [];

        if ($request->recipient_type === 'custom') {
            $phoneNumbers = $request->phone_numbers;
        } else {
            $recipients = $this->getBulkRecipients($request->recipient_type);
            foreach ($recipients as $recipient) {
                if ($recipient->phone) {
                    $phoneNumbers[] = $recipient->phone;
                }
            }
        }

        // Normalize and filter phone numbers to E.164 (PH +63)
        $normalized = [];
        foreach ($phoneNumbers as $p) {
            $np = $this->normalizePhone($p);
            if ($np) {
                $normalized[] = $np;
            }
        }

        if (empty($normalized)) {
            return back()->with('error', 'No valid phone numbers found.');
        }

        $results = $this->smsService->sendBulkSms($normalized, $request->message);
        
        $successCount = 0;
        foreach ($results as $res) {
            if ($res && !isset($res['error'])) {
                $successCount++;
            }
        }
        $totalCount = count($results);

        return back()->with('success', "SMS sent to {$successCount} out of {$totalCount} recipients.");
    }

    public function getBalance()
    {
        $balance = $this->smsService->getBalance();

        if ($balance && !isset($balance['error'])) {
            return response()->json([
                'success' => true,
                'balance' => $balance
            ]);
        }

        // Handle errors
        if (isset($balance['error'])) {
            $statusCode = $balance['status_code'] ?? 500;
            $errorMsg = $balance['error'];
            
            // Parse error message
            if (is_array($errorMsg)) {
                // Check if it's a validation error array like {"apikey":["The selected apikey is invalid."]}
                if (isset($errorMsg['apikey']) && is_array($errorMsg['apikey'])) {
                    $errorMsg = 'Invalid API Key: ' . implode(', ', $errorMsg['apikey']);
                } else {
                    $errorMsg = json_encode($errorMsg);
                }
            }
            
            // Determine appropriate HTTP status code
            $httpCode = match($statusCode) {
                429 => 429,
                401, 403 => 401,
                default => 500
            };
            
            return response()->json([
                'success' => false,
                'message' => $errorMsg,
                'status_code' => $statusCode
            ], $httpCode);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch balance. Check logs for details.'
        ], 500);
    }

    private function getRecipient($type, $id)
    {
        switch ($type) {
            case 'teacher':
                return Teacher::find($id);
            case 'student':
                return Student::find($id);
            case 'guardian':
                return Guardian::find($id);
            default:
                return null;
        }
    }

    private function getBulkRecipients($type)
    {
        switch ($type) {
            case 'teachers':
                return Teacher::whereNotNull('phone')->get();
            case 'students':
                return Student::whereNotNull('phone')->get();
            case 'guardians':
                return Guardian::whereNotNull('phone')->get();
            default:
                return collect();
        }
    }

    /**
     * Normalize Philippine mobile numbers to provider-accepted format (+639XXXXXXXXX or 639XXXXXXXXX).
     * Returns normalized string or null if invalid.
     */
    private function normalizePhone($phone)
    {
        if (!is_string($phone)) {
            return null;
        }
        $p = trim($phone);
        // Remove spaces, dashes, parentheses
        $p = preg_replace('/[\s\-()]/', '', $p);

        // If starts with +63, ensure length
        if (preg_match('/^\+639\d{9}$/', $p)) {
            return $p; // already E.164
        }
        // If starts with 09, convert to +63
        if (preg_match('/^09\d{9}$/', $p)) {
            return '+63' . substr($p, 1); // +639XXXXXXXXX
        }
        // If starts with 639...
        if (preg_match('/^639\d{9}$/', $p)) {
            return '+' . $p; // +639XXXXXXXXX
        }
        // If starts with 9 and has 9 more digits (e.g., 9XXXXXXXXX), add +63
        if (preg_match('/^9\d{9}$/', $p)) {
            return '+63' . $p;
        }
        return null;
    }
}
