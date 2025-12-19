@component('mail::message')
# Hợp đồng đã được ký thành công

Xin chào {{ $contract->tenant->name }},

Bạn đã ký thành công hợp đồng thuê **#{{ $contract->id }}** cho bài đăng **"{{ $contract->listing->title }}"**.

@component('mail::panel')
- Thời gian thuê: {{ \App\Helpers\DateTimeHelper::formatDate($contract->start_date) }} - {{ \App\Helpers\DateTimeHelper::formatDate($contract->end_date) }}
- Giá thuê: {{ number_format($contract->monthly_price) }} VNĐ/tháng
- Chủ trọ: {{ $contract->landlord->name }}
@endcomponent

Bạn có thể xem lại chi tiết hợp đồng và trạng thái thanh toán trong mục **Đặt thuê của tôi**.

@component('mail::button', ['url' => route('bookings.index')])
Xem hợp đồng
@endcomponent

Nếu có thắc mắc, vui lòng liên hệ chủ trọ trên hệ thống hoặc phản hồi trực tiếp qua email này.

Trân trọng,<br>
{{ config('app.name') }}
@endcomponent

