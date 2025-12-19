@extends('layouts.landlord')

@section('page-title')
Chat với {{ $conversation->tenant->name }}
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">
                <i class="bi bi-chat-dots"></i> 
                Chat với {{ $conversation->tenant->name }}
            </h5>
            <small><i class="bi bi-house"></i> {{ $conversation->listing->title ?? 'N/A' }}</small>
        </div>
        <a href="{{ route('chat.index') }}" class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body p-0" style="height: 600px; display: flex; flex-direction: column;">
        <!-- Messages Area -->
        <div id="messagesArea" class="flex-grow-1 p-3 overflow-auto" style="max-height: 500px;">
            @foreach($messages as $message)
            <div class="mb-3 d-flex {{ $message->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                <div class="message-bubble {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%; padding: 10px 15px; border-radius: 18px;">
                    <div class="small mb-1 {{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                        {{ $message->sender->name }}
                    </div>
                    @if($message->file_path)
                        <div class="mb-2">
                            @if($message->file_type === 'image')
                                <a href="{{ \App\Helpers\ImageHelper::url($message->file_path) }}" target="_blank">
                                    <img src="{{ \App\Helpers\ImageHelper::url($message->file_path) }}" alt="{{ $message->file_name }}" class="img-thumbnail" style="max-width: 300px; max-height: 300px; cursor: pointer;">
                                </a>
                            @else
                                <a href="{{ \App\Helpers\ImageHelper::url($message->file_path) }}" target="_blank" class="btn btn-sm {{ $message->sender_id == auth()->id() ? 'btn-light' : 'btn-outline-primary' }}">
                                    <i class="bi bi-file-earmark"></i> {{ $message->file_name }}
                                    <small>({{ number_format($message->file_size / 1024, 2) }} KB)</small>
                                </a>
                            @endif
                        </div>
                    @endif
                    @if($message->message)
                        <div>{{ $message->message }}</div>
                    @endif
                    <div class="small mt-1 {{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                        {{ \App\Helpers\DateTimeHelper::formatDateTime($message->created_at, 'H:i d/m/Y') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

                    <!-- Input Area -->
        <div class="border-top p-3">
            <form id="messageForm" action="{{ route('chat.send', $conversation->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <input type="file" name="file" id="fileInput" class="d-none" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
                    <input type="text" name="message" id="messageInput" class="form-control" placeholder="Nhập tin nhắn...">
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('fileInput').click()" title="Đính kèm file">
                        <i class="bi bi-paperclip"></i>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Gửi
                    </button>
                </div>
                <div id="filePreview" class="mt-2"></div>
                <small class="text-muted">Hỗ trợ: Hình ảnh, PDF, Word, Excel (Tối đa 10MB)</small>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesArea = document.getElementById('messagesArea');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const conversationId = {{ $conversation->id }};
    let lastMessageId = {{ $messages->last()->id ?? 0 }};
    let isSending = false;
    let pollingInterval = null;
    
    // Track message IDs to prevent duplicates
    const existingMessageIds = new Set();
    @php
    foreach($messages as $msg) {
        echo "existingMessageIds.add({$msg->id});\n";
    }
    @endphp

    // Helper function to format datetime
    function formatDateTime(dateString) {
        const date = new Date(dateString);
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${hours}:${minutes} ${day}/${month}/${year}`;
    }

    // Helper function to add message to UI
    function addMessageToUI(msg, scroll = true) {
        // Check for duplicate
        if (existingMessageIds.has(msg.id)) {
            return false;
        }
        existingMessageIds.add(msg.id);
        
                    const isOwn = msg.sender_id == @json(auth()->id());
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-3 d-flex ${isOwn ? 'justify-content-end' : 'justify-content-start'}`;
        messageDiv.setAttribute('data-message-id', msg.id);
        
        let fileHtml = '';
        if (msg.file_path) {
            if (msg.file_type === 'image') {
                fileHtml = `<div class="mb-2"><a href="${msg.file_path}" target="_blank"><img src="${msg.file_path}" alt="${msg.file_name || ''}" class="img-thumbnail" style="max-width: 300px; max-height: 300px; cursor: pointer;"></a></div>`;
            } else {
                fileHtml = `<div class="mb-2"><a href="${msg.file_path}" target="_blank" class="btn btn-sm ${isOwn ? 'btn-light' : 'btn-outline-primary'}"><i class="bi bi-file-earmark"></i> ${msg.file_name || 'File'} <small>(${(msg.file_size / 1024).toFixed(2)} KB)</small></a></div>`;
            }
        }
        
        messageDiv.innerHTML = `
            <div class="message-bubble ${isOwn ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 70%; padding: 10px 15px; border-radius: 18px;">
                <div class="small mb-1 ${isOwn ? 'text-white-50' : 'text-muted'}">${msg.sender.name}</div>
                ${fileHtml}
                ${msg.message ? `<div>${msg.message}</div>` : ''}
                <div class="small mt-1 ${isOwn ? 'text-white-50' : 'text-muted'}">${formatDateTime(msg.created_at)}</div>
            </div>
        `;
        messagesArea.appendChild(messageDiv);
        
        if (scroll) {
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }
        
        return true;
    }

    // File input change handler
    const fileInput = document.getElementById('fileInput');
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('filePreview');
        if (file) {
            const fileSize = (file.size / 1024).toFixed(2);
            preview.innerHTML = `<small class="text-muted"><i class="bi bi-file-earmark"></i> ${file.name} (${fileSize} KB)</small>`;
        } else {
            preview.innerHTML = '';
        }
    });

    // Auto scroll to bottom on load
    messagesArea.scrollTop = messagesArea.scrollHeight;

    // Submit form via AJAX
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (isSending) return; // Prevent double submit
        
        const message = messageInput.value.trim();
        const fileInput = document.getElementById('fileInput');
        const file = fileInput.files[0];
        
        // Phải có ít nhất message hoặc file
        if (!message && !file) {
            alert('Vui lòng nhập tin nhắn hoặc chọn file để gửi.');
            return;
        }

        isSending = true;
        const submitButton = messageForm.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang gửi...';

        // Sử dụng FormData để gửi file
        const formData = new FormData();
        formData.append('message', message);
        if (file) {
            formData.append('file', file);
        }

        fetch(messageForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            // Kiểm tra nếu response không phải JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error('Server trả về HTML thay vì JSON. Có thể do lỗi authentication hoặc route không tồn tại.');
                });
            }
            return response.json();
        })
        .then(data => {
            isSending = false;
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            if (data.success && data.message) {
                messageInput.value = '';
                fileInput.value = '';
                document.getElementById('filePreview').innerHTML = '';
                // Add message immediately without reload
                addMessageToUI(data.message);
                lastMessageId = data.message.id;
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            isSending = false;
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            alert('Có lỗi xảy ra khi gửi tin nhắn: ' + error.message + '. Vui lòng thử lại hoặc reload trang.');
        });
    });

    // Poll for new messages every 2 seconds (faster)
    function pollMessages() {
        if (isSending) return; // Don't poll while sending
        
        fetch(`/chat/${conversationId}/messages?last_message_id=${lastMessageId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Polling: Server trả về HTML thay vì JSON');
                    return null;
                });
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success && data.messages && data.messages.length > 0) {
                let hasNew = false;
                data.messages.forEach(function(msg) {
                    if (addMessageToUI(msg, false)) {
                        hasNew = true;
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    }
                });
                if (hasNew) {
                    messagesArea.scrollTop = messagesArea.scrollHeight;
                }
            }
        })
        .catch(error => {
            console.error('Error fetching messages:', error);
            // Không hiển thị alert cho polling errors để tránh spam
        });
    }
    
    // Start polling
    pollingInterval = setInterval(pollMessages, 2000);
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
});
</script>
@endsection


