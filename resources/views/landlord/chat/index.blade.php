@extends('layouts.landlord')

@section('page-title')
Tin nhắn
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Cuộc trò chuyện</h5>
    </div>
    <div class="card-body p-0">
        @forelse($conversations as $conversation)
        <a href="{{ route('chat.show', $conversation->id) }}" class="text-decoration-none text-dark">
            <div class="border-bottom p-3 hover-bg-light d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-bold">
                        {{ $conversation->tenant->name }}
                    </h6>
                    <p class="mb-0 text-muted small">
                        <i class="bi bi-house"></i> {{ $conversation->listing->title ?? 'N/A' }}
                    </p>
                    @if($conversation->messages->count() > 0)
                    <p class="mb-0 small text-muted mt-1">
                        {{ Str::limit($conversation->messages->first()->message, 60) }}
                    </p>
                    @endif
                </div>
                <div class="text-end">
                    @if($conversation->unread_count > 0)
                    <span class="badge bg-danger mb-2">{{ $conversation->unread_count }}</span>
                    @endif
                    <div class="text-muted small">
                        {{ $conversation->last_message_at ? \App\Helpers\DateTimeHelper::diffForHumans($conversation->last_message_at) : '' }}
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="p-5 text-center text-muted">
            <i class="bi bi-chat fs-1 d-block mb-3"></i>
            <p>Chưa có cuộc trò chuyện nào</p>
        </div>
        @endforelse
    </div>
</div>

<style>
.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}
</style>
@endsection

