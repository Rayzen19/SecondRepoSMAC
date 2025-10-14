@extends('admin.components.template')

@section('content')
<div class="card">
    <div class="card-header"><h5>Compose Message</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.messages.send') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">To</label>
                <select name="recipients[]" class="form-control" multiple required>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="body" class="form-control" rows="6" required></textarea>
            </div>
            <button class="btn btn-primary">Send</button>
        </form>
    </div>
</div>
@endsection
