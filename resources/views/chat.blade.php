<x-app-layout>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
.wrap { display: flex; height: calc(100vh - 64px); }
.sidebar {
    width: 220px; border-right: 1px solid #e5e7eb;
    background: #f9fafb; display: flex; flex-direction: column;
}
.sidebar h3 { padding: 16px; font-weight: 700; font-size: 16px; }
.sidebar-section-title {
    padding: 8px 16px 4px; font-size: 11px; font-weight: 600;
    text-transform: uppercase; color: #9ca3af;
    letter-spacing: 0.05em; margin-top: 8px;
}
.sidebar a { text-decoration: none; display: block; color: #111; }
.sidebar a div { padding: 10px 16px; border-radius: 8px; margin: 2px 8px; }
.sidebar a:hover div { background: #f3f4f6; }
.sidebar a.active div { background: #e0f2f1; font-weight: bold; color: #1D9E75; }
.divider { height: 1px; background: #e5e7eb; margin: 8px 16px; }
.chat-area { flex: 1; display: flex; flex-direction: column; }

.chat-header {
    padding: 14px 16px; border-bottom: 1px solid #ddd;
    font-weight: bold; font-size: 15px; background: #fff;
    display: flex; flex-direction: column; gap: 2px;
}
.chat-header-status {
    font-size: 11px; font-weight: 400; color: #9ca3af;
}
.chat-header-status.online { color: #1D9E75; }

#chat-box {
    flex: 1; overflow-y: auto; padding: 16px;
    background: #f0f4f8; display: flex;
    flex-direction: column; gap: 8px;
}
.msg {
    max-width: 60%; padding: 8px 12px; border-radius: 12px;
    font-size: 13px; line-height: 1.5; word-break: break-word;
}
.me { align-self: flex-end; background: #60a5fa; color: white; border-bottom-right-radius: 4px; }
.them { align-self: flex-start; background: white; border: 1px solid #ddd; border-bottom-left-radius: 4px; }
.sender-name { font-size: 11px; font-weight: 600; color: #60a5fa; margin-bottom: 2px; display: block; }
.form-wrap { display: flex; padding: 10px; border-top: 1px solid #ddd; background: #fff; }
.form-wrap input {
    flex: 1; padding: 10px 16px; border-radius: 20px;
    border: 1px solid #ccc; outline: none; font-size: 13px;
}
.form-wrap button {
    margin-left: 8px; padding: 10px 20px; background: #fbbf24;
    color: white; border: none; border-radius: 20px;
    cursor: pointer; font-size: 13px;
}

/* Online dot di sidebar */
.user-item { display: flex; align-items: center; justify-content: space-between; }
.dot { width: 8px; height: 8px; border-radius: 50%; background: #d1d5db; flex-shrink: 0; }
.dot.online { background: #1D9E75; }
</style>

<div class="wrap">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h3>ChatApp</h3>

        <div class="sidebar-section-title">Private</div>
        @foreach($users as $user)
            <a href="{{ route('chat.user', $user->id) }}"
               class="{{ isset($id) && !$isGroup && $id == $user->id ? 'active' : '' }}">
                <div class="user-item">
                    <span>{{ $user->name }}</span>
                    <span class="dot" id="dot-{{ $user->id }}"></span>
                </div>
            </a>
        @endforeach

        <div class="divider"></div>

<div class="sidebar-section-title">👥 Groups</div>

<a href="{{ url('/group/create') }}">
    <div style="color:#1D9E75; font-weight:bold;">
        + Tambah Grup
    </div>
</a>

@foreach($groups as $group)
    <a href="{{ route('chat.group', $group->id) }}"
       class="{{ isset($id) && $isGroup && $id == $group->id ? 'active' : '' }}">
        <div>{{ $group->name }}</div>
    </a>
@endforeach
    </div>

    <!-- CHAT AREA -->
    <div class="chat-area">

        <div class="chat-header">
            @if(isset($id))
                @if($isGroup)
                    <span>{{ \App\Models\Conversation::find($id)->name ?? 'Group' }}</span>
                @else
                    <span>{{ $users->firstWhere('id', $id)->name ?? 'User' }}</span>
                    <span class="chat-header-status" id="header-status">offline</span>
                @endif
            @else
                <span>Pilih chat</span>
            @endif
        </div>

        <div id="chat-box">
            @foreach($messages as $msg)
                @if($msg->user_id == Auth::id())
                    <div class="msg me">{{ $msg->message }}</div>
                @else
                    <div class="msg them">
                        @if($isGroup)
                            <span class="sender-name">{{ $msg->user->name ?? 'User' }}</span>
                        @endif
                        {{ $msg->message }}
                    </div>
                @endif
            @endforeach
        </div>

        @if(isset($id))
        <div class="form-wrap">
            <form method="POST" action="{{ route('chat.store') }}" style="display:flex; width:100%;">
                @csrf
                @if($isGroup)
                    <input type="hidden" name="conversation_id" value="{{ $id }}">
                @else
                    <input type="hidden" name="receiver_id" value="{{ $id }}">
                @endif
                <input type="text" name="message" placeholder="Tulis pesan..." required autocomplete="off">
                <button type="submit">Kirim</button>
            </form>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const box = document.getElementById('chat-box');
    const myId = {{ Auth::id() }};
    const isGroup = {{ $isGroup ? 'true' : 'false' }};
    const chatId = {{ $id ?? 'null' }};

    if (box) box.scrollTop = box.scrollHeight;

    window.Echo.join('online')
        .here((users) => {
            users.forEach(u => setOnline(u.id, true));
        })
        .joining((user) => {
            setOnline(user.id, true);
        })
        .leaving((user) => {
            setOnline(user.id, false);
        });

    function setOnline(userId, isOnline) {
        const dot = document.getElementById('dot-' + userId);
        if (dot) dot.classList.toggle('online', isOnline);

        const headerStatus = document.getElementById('header-status');
        if (headerStatus && userId == chatId) {
            headerStatus.textContent = isOnline ? 'online' : 'offline';
            headerStatus.classList.toggle('online', isOnline);
        }
    }

    if (!chatId) return;

    function appendMessage(data, isMe) {
        const msg = document.createElement('div');
        msg.className = isMe ? 'msg me' : 'msg them';

        if (!isMe && isGroup) {
            const name = document.createElement('span');
            name.className = 'sender-name';
            name.textContent = data.user_name ?? 'User';
            msg.appendChild(name);
        }

        msg.appendChild(document.createTextNode(data.message));
        box.appendChild(msg);
        box.scrollTop = box.scrollHeight;
    }

    if (isGroup) {
        window.Echo.private('group.' + chatId)
            .listen('.message.sent', (e) => {
                if (e.message.user_id != myId) appendMessage(e.message, false);
            });
    } else {
        window.Echo.private('chat.' + myId)
            .listen('.message.sent', (e) => {
                if (e.message.user_id != myId && e.message.user_id == chatId) {
                    appendMessage(e.message, false);
                }
            });
    }

    const form = document.querySelector('.form-wrap form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const input = form.querySelector('input[name="message"]');
            const messageText = input.value.trim();
            if (!messageText) return;

            appendMessage({ message: messageText }, true);
            input.value = '';

            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('message', messageText);
            @if($isGroup)
                fd.append('conversation_id', chatId);
            @else
                fd.append('receiver_id', chatId);
            @endif

            fetch('{{ route('chat.store') }}', { method: 'POST', body: fd });
        });
    }
});
</script>
@endpush

</x-app-layout>