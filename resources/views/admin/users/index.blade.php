@extends('layouts.admin')

@section('page-title', 'Quản lý người dùng')

@section('content')
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people"></i> Quản lý người dùng</h5>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Thêm người dùng
        </a>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="agent" {{ request('role') == 'agent' ? 'selected' : '' }}>Tác nhân</option>
                        <option value="landlord" {{ request('role') == 'landlord' ? 'selected' : '' }}>Chủ trọ</option>
                        <option value="tenant" {{ request('role') == 'tenant' ? 'selected' : '' }}>Người tìm phòng</option>
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm kiếm theo tên, email...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm
                    </button>
                </div>
            </div>
        </form>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Số dư</th>
                        <th>Ngày đăng ký</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td><strong>#{{ $user->id }}</strong></td>
                        <td>
                            <div class="fw-bold">{{ $user->name }}</div>
                        </td>
                        <td><i class="bi bi-envelope text-muted"></i> {{ $user->email }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge bg-danger"><i class="bi bi-shield-check"></i> Admin</span>
                            @elseif($user->role == 'agent')
                                <span class="badge bg-warning"><i class="bi bi-shield-check"></i> Tác nhân</span>
                            @elseif($user->role == 'landlord')
                                <span class="badge bg-success"><i class="bi bi-person-badge"></i> Chủ trọ</span>
                            @else
                                <span class="badge bg-info"><i class="bi bi-person"></i> Người tìm phòng</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-success"><i class="bi bi-wallet2"></i> {{ number_format($user->balance) }} VNĐ</strong>
                        </td>
                        <td><i class="bi bi-calendar3"></i> {{ \App\Helpers\DateTimeHelper::formatDate($user->created_at) }}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-primary" title="Xem">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->canEdit())
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-success" title="Sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if(auth()->user()->canDelete() && $user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa người dùng này?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Không có người dùng nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
