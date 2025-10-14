@extends('admin.components.template')

@section('content')
<div class="card">
    <div class="card-header"><h5>Inbox</h5></div>
    <div class="card-body">
        <a href="{{ route('admin.messages.compose') }}" class="btn btn-primary mb-3">Compose</a>

        @if($recipients->isEmpty())
            <p>No messages</p>
        @else
            <ul class="list-group">
                @foreach($recipients as $r)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $r->message->subject ?? '(no subject)' }}</strong>
                            <div class="text-muted small">From: {{ optional($r->message->sender)->name ?? 'System' }} — {{ $r->created_at->diffForHumans() }}</div>
                        </div>
                        <div>
                            <a href="{{ route('admin.messages.show', $r->id) }}" class="btn btn-sm btn-outline-primary">Open</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection
