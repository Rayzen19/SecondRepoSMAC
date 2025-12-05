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
            'message' => 'required|string|max:480',
        ]);

        $phone = $this->normalizePhone($request->phone);
        if (!$phone) {
            return back()->with('error', 'Invalid phone number format. Use 09XXXXXXXXX or +639XXXXXXXXX.');
        }

        $result = $this->smsService->sendSms($phone, $request->message);

        // When multipart, service returns array of segment results
        $segments = is_array($result) ? $result : [$result];
        $sent = 0; $failed = 0; $errors = [];
        foreach ($segments as $seg) {
            if (is_array($seg) && !isset($seg['error'])) {
                $first = $seg[0] ?? [];
                $status = $first['status'] ?? null;
                $hasId = isset($first['message_id']);
                if ($status === 'Queued' || $status === 'Pending' || $status === 'Sent' || $hasId) {
                    $sent++;
                } else {
                    $failed++;
                    $errors[] = $this->extractError($seg);
                }
            } else {
                $failed++;
                $errors[] = is_array($seg) && isset($seg['error']) ? $seg['error'] : 'Unknown provider error';
            }
        }

        if ($sent > 0 && $failed === 0) {
            return back()->with('success', "Test SMS sent successfully (segments: {$sent}).");
        }
        if ($sent > 0 && $failed > 0) {
            return back()->with('success', "Sent {$sent} segment(s), {$failed} failed: " . implode('; ', array_filter($errors)));
        }
        // All failed
        $errorMsg = !empty($errors) ? implode('; ', array_filter($errors)) : 'Failed to send SMS. Please check your number and API settings.';
        return back()->with('error', $errorMsg);
    }

    public function sendSingle(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:teacher,student,guardian',
            'recipient_id' => 'required|integer',
            'message' => 'required|string|max:480',
        ]);

        $recipient = $this->getRecipient($request->recipient_type, $request->recipient_id);

        if (!$recipient || !$recipient->phone) {
            return back()->with('error', 'Recipient phone number not found.');
        }

        $phone = $this->normalizePhone($recipient->phone);
        if (!$phone) {
            return back()->with('error', 'Recipient phone is invalid. Use 09XXXXXXXXX or +639XXXXXXXXX.');
        }

        $result = $this->smsService->sendSms($phone, $request->message);

        $segments = is_array($result) ? $result : [$result];
        $sent = 0; $failed = 0; $errors = [];
        foreach ($segments as $seg) {
            if (is_array($seg) && !isset($seg['error'])) {
                $first = $seg[0] ?? [];
                $status = $first['status'] ?? null;
                $hasId = isset($first['message_id']);
                if ($status === 'Queued' || $status === 'Pending' || $status === 'Sent' || $hasId) {
                    $sent++;
                } else {
                    $failed++;
                    $errors[] = $this->extractError($seg);
                }
            } else {
                $failed++;
                $errors[] = is_array($seg) && isset($seg['error']) ? $seg['error'] : 'Unknown provider error';
            }
        }

        if ($sent > 0 && $failed === 0) {
            return back()->with('success', "SMS sent to {$recipient->first_name} {$recipient->last_name} (segments: {$sent}).");
        }
        if ($sent > 0 && $failed > 0) {
            return back()->with('success', "Sent {$sent} segment(s), {$failed} failed: " . implode('; ', array_filter($errors)));
        }
        $errorMsg = !empty($errors) ? implode('; ', array_filter($errors)) : 'Failed to send SMS.';
        return back()->with('error', $errorMsg);
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'recipient_type' => 'required|in:teachers,students,guardians,custom',
            'message' => 'required|string|max:480',
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
        
        $successCount = 0; $totalCount = 0;
        foreach ($results as $perNumber) {
            $segments = is_array($perNumber) ? $perNumber : [$perNumber];
            $totalCount++;
            $anySuccess = false;
            foreach ($segments as $seg) {
                if (is_array($seg) && !isset($seg['error'])) {
                    $first = $seg[0] ?? [];
                    $status = $first['status'] ?? null;
                    $hasId = isset($first['message_id']);
                    if ($status === 'Queued' || $status === 'Pending' || $status === 'Sent' || $hasId) {
                        $anySuccess = true; break;
                    }
                }
            }
            if ($anySuccess) $successCount++;
        }

        return back()->with('success', "SMS sent to {$successCount} out of {$totalCount} recipients.");
    }

    private function extractError($result): string
    {
        if (!is_array($result)) return 'Unknown provider error';
        if (isset($result[0]['status']) && $result[0]['status'] === 'Failed' && isset($result[0]['message'])) {
            return (string)$result[0]['message'];
        }
        if (isset($result[0]['error'])) {
            return (string)$result[0]['error'];
        }
        if (isset($result['error'])) {
            return is_string($result['error']) ? $result['error'] : json_encode($result['error']);
        }
        if (isset($result[0]['status']) && isset($result[0]['status_message'])) {
            return (string)$result[0]['status_message'];
        }
        if (isset($result['number'])) {
            $err = $result['number'];
            return is_array($err) ? implode(', ', $err) : (string)$err;
        }
        if (isset($result['message'])) {
            $err = $result['message'];
            return is_array($err) ? implode(', ', $err) : (string)$err;
        }
        if (isset($result['sendername'])) {
            $err = $result['sendername'];
            return is_array($err) ? ('Invalid sender name: ' . implode(', ', $err)) : ('Invalid sender name: ' . $err);
        }
        return 'Unknown provider error';
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
