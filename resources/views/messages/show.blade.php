@extends('admin.components.template')

@section('content')
<div class="card">
    <div class="card-header"><h5>{{ $recipient->message->subject ?? '(no subject)' }}</h5></div>
    <div class="card-body">
    <div class="mb-2 text-muted">From: {{ optional($recipient->message->sender)->name ?? 'System' }} — {{ $recipient->created_at->toDayDateTimeString() }}</div>
        <div class="mb-4">{!! nl2br(e($recipient->message->body)) !!}</div>
    <a href="{{ route('admin.messages.messenger') }}" class="btn btn-secondary">Back to Messages</a>
    </div>
</div>
@endsection
