@extends('admin.components.template')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Messages</h5>
                <a href="{{ route('admin.messages.compose') }}" class="btn btn-sm btn-primary">New</a>
            </div>
            <div class="card-body p-0">
                <ul id="conversation-list" class="list-group list-group-flush">
                    @foreach($partners as $p)
                    <li class="list-group-item conversation-item" data-user-id="{{ $p->id }}">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $p->name }}</strong>
                                <div class="small text-muted">{{ $p->email }}</div>
                            </div>
                            <div class="text-end">
                                <!-- placeholder for unread badge -->
                                <span class="badge bg-primary d-none" id="unread-{{ $p->id }}">0</span>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">
                <div id="thread-header">Select a conversation</div>
            </div>
            <div class="card-body overflow-auto" id="thread-body" style="height:60vh">
                <div id="thread-messages" class="d-flex flex-column gap-3"></div>
            </div>
            <div class="card-footer">
                <form id="send-form">
                    @csrf
                    <input type="hidden" name="to" id="to-user-id">
                    <div class="input-group">
                        <input id="message-input" name="body" class="form-control" placeholder="Type a message...">
                        <button class="btn btn-primary" type="submit">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const list = document.getElementById('conversation-list');
        const threadHeader = document.getElementById('thread-header');
        const threadMessages = document.getElementById('thread-messages');
        const toInput = document.getElementById('to-user-id');
        const sendForm = document.getElementById('send-form');
        const messageInput = document.getElementById('message-input');

    let currentUserId = null;
    const ME_ID = "{{ Auth::id() ?? '' }}";

        function formatTime(dt) {
            const d = new Date(dt);
            return d.toLocaleString();
        }

        function loadConversation(userId) {
            fetch("{{ url('/admin/messenger/conversation/') }}/" + userId)
                .then(r => r.json())
                .then(data => {
                    currentUserId = userId;
                    toInput.value = userId;
                    threadHeader.textContent = data.conversation_with.name || '';
                    threadMessages.innerHTML = '';
                    data.messages.forEach(m => {
                        const div = document.createElement('div');
                        div.className = 'p-2 rounded';
                        if (String(m.from) === ME_ID) {
                            div.classList.add('bg-primary', 'text-white', 'align-self-end');
                        } else {
                            div.classList.add('bg-light', 'text-dark', 'align-self-start');
                        }
                        div.innerHTML = '<div>' + (m.subject ? '<strong>' + (m.subject) + '</strong><br>' : '') + nl2br(escapeHtml(m.body)) + '</div><div class="small text-muted mt-1">' + formatTime(m.created_at) + '</div>';
                        threadMessages.appendChild(div);
                    });
                    threadMessages.scrollTop = threadMessages.scrollHeight;
                });
        }

        function nl2br (str) {
            return str.replace(/\n/g, '<br>');
        }

        function escapeHtml(unsafe) {
            return unsafe
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/\"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        list.addEventListener('click', function(e){
            const li = e.target.closest('.conversation-item');
            if (!li) return;
            const userId = li.dataset.userId;
            loadConversation(userId);
        });

        sendForm.addEventListener('submit', function(e){
            e.preventDefault();
            if (!currentUserId) return alert('Select a conversation first');
            const body = messageInput.value.trim();
            if (!body) return;
            fetch("{{ route('admin.messages.sendConversation') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ to: currentUserId, body: body })
            }).then(r => r.json()).then(res => {
                // Append message
                const m = res.message;
                const div = document.createElement('div');
                div.className = 'p-2 rounded bg-primary text-white align-self-end';
                div.innerHTML = '<div>' + nl2br(escapeHtml(m.body)) + '</div><div class="small text-muted mt-1">' + new Date(m.created_at).toLocaleString() + '</div>';
                threadMessages.appendChild(div);
                messageInput.value = '';
                threadMessages.scrollTop = threadMessages.scrollHeight;
            }).catch(err => { console.error(err); alert('Failed to send message'); });
        });

    })();
</script>
@endpush

@endsection
