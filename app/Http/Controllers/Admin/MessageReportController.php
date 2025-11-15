<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageReport;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageReportController extends Controller
{
    /**
     * Display a listing of message reports
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = MessageReport::with(['message.sender', 'reporter', 'reviewer'])
            ->orderByDesc('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(20);
        
        $stats = [
            'total' => MessageReport::count(),
            'pending' => MessageReport::where('status', 'pending')->count(),
            'reviewed' => MessageReport::where('status', 'reviewed')->count(),
            'dismissed' => MessageReport::where('status', 'dismissed')->count(),
            'action_taken' => MessageReport::where('status', 'action_taken')->count(),
        ];

        return view('admin.message-reports.index', compact('reports', 'stats', 'status'));
    }

    /**
     * Display the specified report
     */
    public function show(MessageReport $report)
    {
        $report->load(['message.sender', 'message.recipients.recipient', 'reporter', 'reviewer']);
        
        return view('admin.message-reports.show', compact('report'));
    }

    /**
     * Update report status
     */
    public function updateStatus(Request $request, MessageReport $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:reviewed,dismissed,action_taken',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report status updated successfully',
        ]);
    }

    /**
     * Delete reported message
     */
    public function deleteMessage(MessageReport $report)
    {
        $message = $report->message;
        
        if (!$message) {
            return response()->json([
                'success' => false,
                'error' => 'Message not found'
            ], 404);
        }

        // Delete attachment if exists
        if ($message->attachment_path) {
            $filePath = storage_path('app/public/' . $message->attachment_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete the message
        $message->delete();

        // Update report status
        $report->update([
            'status' => 'action_taken',
            'admin_notes' => ($report->admin_notes ?? '') . "\n[Action: Message deleted]",
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }

    /**
     * Dismiss report
     */
    public function dismiss(Request $request, MessageReport $report)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $report->update([
            'status' => 'dismissed',
            'admin_notes' => $validated['admin_notes'] ?? 'Report dismissed - no violation found',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report dismissed successfully',
        ]);
    }
}
