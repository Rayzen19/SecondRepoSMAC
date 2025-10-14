<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\MessageRecipient;
use App\Models\User;

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

        return view('messages.messenger', compact('partners'));
    }

    public function conversation(\App\Models\User $user)
    {
    $me = Auth::user();

        // Messages where I am the sender and they are a recipient
        $sent = Message::where('sender_id', $me->id)
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
            'body' => 'required|string',
            'subject' => 'nullable|string|max:255',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'],
        ]);

        MessageRecipient::create(['message_id' => $message->id, 'recipient_id' => $data['to']]);

        // Return the created message as JSON for immediate UI append
        return response()->json(['message' => $message->fresh('sender')]);
    }
}
