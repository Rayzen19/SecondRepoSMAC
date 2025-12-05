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

        $result = $this->smsService->sendSms(
            $request->phone,
            $request->message
        );

        if ($result && !isset($result['error'])) {
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

        $result = $this->smsService->sendSms(
            $recipient->phone,
            $request->message
        );

        if ($result && !isset($result['error'])) {
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

        if (empty($phoneNumbers)) {
            return back()->with('error', 'No valid phone numbers found.');
        }

        $results = $this->smsService->sendBulkSms($phoneNumbers, $request->message);
        
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
}
