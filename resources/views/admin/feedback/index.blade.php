@extends('layouts.admin')

@section('page-title', 'Quản lý phản hồi')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Quản lý phản hồi</h5>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('admin.feedback.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <select name="is_resolved" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="0" {{ request('is_resolved') == '0' ? 'selected' : '' }}>Chưa xử lý</option>
                        <option value="1" {{ request('is_resolved') == '1' ? 'selected' : '' }}>Đã xử lý</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           class="form-control" 
                           placeholder="Tìm kiếm theo tên, email, tiêu đề...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </div>
        </form>

        <!-- Feedback List -->
        <div class="row g-4">
            @forelse($feedbacks as $feedback)
            <div class="col-12">
                <div class="card {{ !$feedback->is_resolved ? 'border-warning border-start border-4' : 'border-success border-start border-4' }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <h5 class="mb-0"><i class="bi bi-envelope-paper"></i> {{ $feedback->subject }}</h5>
                                    @if($feedback->is_resolved)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Đã xử lý
                                    </span>
                                    @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock-history"></i> Chưa xử lý
                                    </span>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <p class="mb-1">
                                        <i class="bi bi-person"></i> <strong>Từ:</strong> {{ $feedback->name }} 
                                        <span class="text-muted">(<i class="bi bi-envelope"></i> {{ $feedback->email }})</span>
                                    </p>
                                    @if($feedback->phone)
                                    <p class="mb-1">
                                        <i class="bi bi-telephone"></i> <strong>Điện thoại:</strong> {{ $feedback->phone }}
                                    </p>
                                    @endif
                                    @if($feedback->user)
                                    <p class="mb-1">
                                        <i class="bi bi-person-circle"></i> <strong>Người dùng:</strong> 
                                        {{ $feedback->user->name }} ({{ $feedback->user->email }})
                                    </p>
                                    @endif
                                </div>
                                
                                <p class="text-muted mb-2">{{ $feedback->message }}</p>
                                
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($feedback->created_at) }}
                                    <i class="bi bi-clock ms-2"></i> {{ \App\Helpers\DateTimeHelper::formatTime($feedback->created_at) }}
                                </small>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.feedback.show', $feedback->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                    @if(!$feedback->is_resolved)
                                    <form action="{{ route('admin.feedback.mark-processed', $feedback->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="bi bi-check-circle"></i> Đánh dấu đã xử lý
                                        </button>
                                    </form>
                                    @endif
                                    <form action="{{ route('admin.feedback.destroy', $feedback->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Bạn có chắc muốn xóa phản hồi này?')">
                                            <i class="bi bi-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Chưa có phản hồi nào
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($feedbacks->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $feedbacks->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
