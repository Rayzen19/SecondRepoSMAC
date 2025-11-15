<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\User;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function inbox()
    {
        $userId = Auth::id();
        $recipients = MessageRecipient::with('message.sender')
            ->where('recipient_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return view('messages.inbox', ['recipients' => $recipients]);
    }

    public function compose()
    {
        // list possible recipients (all users) - small app; later refine by role
        $users = User::orderBy('name')->get();
        return view('messages.compose', ['users' => $users]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'integer|exists:users,id',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'],
        ]);

        foreach ($data['recipients'] as $rid) {
            MessageRecipient::create(['message_id' => $message->id, 'recipient_id' => $rid]);
            
            // Broadcast the message to the recipient
            broadcast(new MessageSent($message->load('sender'), $rid))->toOthers();
        }

        return redirect()->route('admin.messages.messenger')->with('success', 'Message sent');
    }

    public function show(MessageRecipient $recipient)
    {
        // mark as read when opened
        if (!$recipient->read_at) {
            $recipient->read_at = now();
            $recipient->save();
        }

        $recipient->load('message.sender');
        return view('messages.show', ['recipient' => $recipient]);
    }

    // Messenger-style UI
    public function messenger()
    {
        // list unique conversation partners for current user
        $userId = Auth::id();

        // Gather partners from messages sent or received
        $sentPartnerIds = Message::where('sender_id', $userId)->with('recipients')->get()->pluck('recipients.*.recipient_id')->flatten()->unique();
        $receivedPartnerIds = MessageRecipient::where('recipient_id', $userId)->with('message')->get()->pluck('message.sender_id')->flatten()->unique();

        $partnerIds = $sentPartnerIds->merge($receivedPartnerIds)->filter()->unique()->values();

        $partners = \App\Models\User::whereIn('id', $partnerIds)->orderBy('name')->get();

        // Calculate unread counts for each partner
        $partners = $partners->map(function($partner) use ($userId) {
            $unreadCount = MessageRecipient::where('recipient_id', $userId)
                ->whereNull('read_at')
                ->whereHas('message', function($q) use ($partner) {
                    $q->where('sender_id', $partner->id);
                })
                ->count();
            
            $partner->unread_count = $unreadCount;
            return $partner;
        });

        return view('admin.messages.messenger', compact('partners'));
    }

    public function conversation(\App\Models\User $user)
    {
        $me = Auth::user();

        // Mark all unread messages from this user as read
        MessageRecipient::where('recipient_id', $me->id)
            ->whereNull('read_at')
            ->whereHas('message', function ($q) use ($user) {
                $q->where('sender_id', $user->id);
            })
            ->update(['read_at' => now()]);

        // Messages where I am the sender and they are a recipient
        $sent = Message::select('messages.*')
            ->where('sender_id', $me->id)
            ->whereHas('recipients', function ($q) use ($user) {
                $q->where('recipient_id', $user->id);
            })->with('recipients')->get();

        // Messages where they are the sender and I am a recipient
        $receivedRecipients = MessageRecipient::where('recipient_id', $me->id)
            ->whereHas('message', function ($q) use ($user) {
                $q->where('sender_id', $user->id);
            })->with('message.sender')->get();

        // Normalize into a single timeline ordered by created_at
        $timeline = collect();

        foreach ($sent as $m) {
            $timeline->push([
                'id' => $m->id,
                'from' => $me->id,
                'to' => $user->id,
                'body' => $m->body,
                'subject' => $m->subject,
                'created_at' => $m->created_at,
                'attachment_path' => $m->attachment_path ?? null,
                'attachment_name' => $m->attachment_name ?? null,
                'attachment_type' => $m->attachment_type ?? null,
                'attachment_size' => $m->attachment_size ?? null,
            ]);
        }

        foreach ($receivedRecipients as $r) {
            $m = $r->message;
            $timeline->push([
                'id' => $m->id,
                'from' => $m->sender_id,
                'to' => $me->id,
                'body' => $m->body,
                'subject' => $m->subject,
                'created_at' => $r->created_at,
                'attachment_path' => $m->attachment_path ?? null,
                'attachment_name' => $m->attachment_name ?? null,
                'attachment_type' => $m->attachment_type ?? null,
                'attachment_size' => $m->attachment_size ?? null,
            ]);
        }

        $timeline = $timeline->sortBy('created_at')->values();

        return response()->json([
            'conversation_with' => $user,
            'messages' => $timeline,
        ]);
    }

    public function sendConversation(Request $request)
    {
        $data = $request->validate([
            'to' => 'required|integer|exists:users,id',
            'body' => 'nullable|string',
            'subject' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Require either body or attachment
        if (empty($data['body']) && !$request->hasFile('attachment')) {
            return response()->json([
                'success' => false,
                'error' => 'Please provide a message or attachment'
            ], 422);
        }

        $attachmentData = [];
        
        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('message_attachments', 'public');
            
            $attachmentData = [
                'attachment_path' => $path,
                'attachment_name' => $originalName,
                'attachment_type' => $file->getMimeType(),
                'attachment_size' => $file->getSize(),
            ];
        }

        $message = Message::create(array_merge([
            'sender_id' => Auth::id(),
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'] ?? '(File attachment)',
        ], $attachmentData));

        MessageRecipient::create(['message_id' => $message->id, 'recipient_id' => $data['to']]);

        // Try to broadcast the message to the recipient (will fail gracefully if Pusher not configured)
        try {
            broadcast(new MessageSent($message->load('sender'), $data['to']))->toOthers();
        } catch (\Exception $e) {
            Log::warning('Broadcasting failed: ' . $e->getMessage());
        }

        // Return the message in the format expected by the frontend
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'from' => $message->sender_id,
                'from_id' => $message->sender_id,
                'to' => $data['to'],
                'body' => $message->body,
                'subject' => $message->subject,
                'created_at' => $message->created_at->toISOString(),
                'attachment_path' => $message->attachment_path,
                'attachment_name' => $message->attachment_name,
                'attachment_type' => $message->attachment_type,
                'attachment_size' => $message->attachment_size,
            ]
        ]);
    }

    // Download attachment
    public function downloadAttachment(Message $message)
    {
        // Check if user has access to this message
        $userId = Auth::id();
        $hasAccess = $message->sender_id === $userId || 
                     $message->recipients()->where('recipient_id', $userId)->exists();
        
        if (!$hasAccess) {
            abort(403, 'Unauthorized access to attachment');
        }

        if (!$message->attachment_path) {
            abort(404, 'No attachment found');
        }

        $path = storage_path('app/public/' . $message->attachment_path);
        
        if (!file_exists($path)) {
            abort(404, 'Attachment file not found');
        }

        return response()->download($path, $message->attachment_name);
    }

    // API: Get all users for dropdown selection
    public function getAllUsers()
    {
        $users = User::select('id', 'name', 'email')
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();
        
        return response()->json($users);
    }

    // Unsend (delete) message
    public function unsendMessage(Message $message)
    {
        $userId = Auth::id();
        
        // Only the sender can unsend their message
        if ($message->sender_id !== $userId) {
            return response()->json([
                'success' => false,
                'error' => 'You can only unsend your own messages'
            ], 403);
        }

        // Delete attachment file if exists
        if ($message->attachment_path) {
            $filePath = storage_path('app/public/' . $message->attachment_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete the message (will cascade delete recipients)
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    // Get unread message count
    public function getUnreadCount()
    {
        $userId = Auth::id();
        $unreadCount = MessageRecipient::where('recipient_id', $userId)
            ->whereNull('read_at')
            ->count();
        
        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

    // Get unread counts by conversation partner
    public function getUnreadCountsByPartner()
    {
        $userId = Auth::id();
        
        // Get unread counts grouped by sender
        $unreadCounts = MessageRecipient::where('recipient_id', $userId)
            ->whereNull('read_at')
            ->join('messages', 'message_recipients.message_id', '=', 'messages.id')
            ->select('messages.sender_id', \DB::raw('count(*) as unread_count'))
            ->groupBy('messages.sender_id')
            ->pluck('unread_count', 'sender_id')
            ->toArray();
        
        return response()->json([
            'success' => true,
            'unread_counts' => $unreadCounts
        ]);
    }
}
