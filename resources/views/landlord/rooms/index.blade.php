@extends('layouts.landlord')

@section('page-title', 'Quản lý phòng')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="bi bi-house-door"></i> Quản lý phòng
            </h2>
            <p class="text-muted">Danh sách phòng đang cho thuê</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($contracts->count() > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Phòng trọ</th>
                                <th>Người thuê</th>
                                <th>Ngày bắt đầu</th>
                                <th>Ngày kết thúc</th>
                                <th>Giá thuê/tháng</th>
                                <th>Hóa đơn</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contracts as $contract)
                            <tr>
                                <td>
                                    <strong>{{ $contract->listing->title }}</strong><br>
                                    <small class="text-muted">{{ $contract->listing->address }}</small>
                                </td>
                                <td>
                                    <strong>{{ $contract->tenant->name }}</strong><br>
                                    <small class="text-muted">{{ $contract->tenant->email }}</small><br>
                                    <small class="text-muted">{{ $contract->tenant->phone ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                                <td>{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'N/A' }}</td>
                                <td>{{ number_format($contract->monthly_price, 0, ',', '.') }}₫</td>
                                <td>
                                    @php
                                        $bills = $contract->utilityBills;
                                        $pendingBills = $bills->where('payment_status', 'pending')->count();
                                        $paidBills = $bills->where('payment_status', 'paid')->count();
                                    @endphp
                                    <span class="badge bg-warning">{{ $pendingBills }} chưa thanh toán</span>
                                    <span class="badge bg-success">{{ $paidBills }} đã thanh toán</span>
                                </td>
                                <td>
                                    <a href="{{ route('landlord.rooms.create-bill', $contract->id) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="bi bi-receipt"></i> Tạo hóa đơn
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $contracts->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                <p class="text-muted mt-3">Chưa có phòng nào đang cho thuê</p>
                <a href="{{ route('landlord.bookings.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Quay lại đơn đặt thuê
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

