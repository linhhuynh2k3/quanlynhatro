<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Thông báo hóa đơn đến hạn</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #27ae60;">Thông báo hóa đơn đến hạn thanh toán</h2>
        
        <p>Xin chào <strong>{{ $invoice->tenant->name }}</strong>,</p>
        
        <p>Bạn có một hóa đơn sắp đến hạn thanh toán:</p>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Số hóa đơn:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Ngày lập:</strong> {{ \App\Helpers\DateTimeHelper::formatDate($invoice->invoice_date) }}</p>
            <p><strong>Hạn thanh toán:</strong> {{ \App\Helpers\DateTimeHelper::formatDate($invoice->due_date) }}</p>
            <p><strong>Kỳ thanh toán:</strong> {{ \App\Helpers\DateTimeHelper::formatDate($invoice->period_start) }} - {{ \App\Helpers\DateTimeHelper::formatDate($invoice->period_end) }}</p>
            <p><strong>Phòng trọ:</strong> {{ $invoice->contract->listing->title ?? 'N/A' }}</p>
            <p style="font-size: 18px; color: #e74c3c; margin-top: 10px;">
                <strong>Tổng tiền: {{ number_format($invoice->total_amount) }} VNĐ</strong>
            </p>
        </div>

        <h3>Chi tiết hóa đơn:</h3>
        <ul>
            <li>Tiền trọ: {{ number_format($invoice->rent_amount) }} VNĐ</li>
            @if($invoice->electricity_amount > 0)
            <li>Điện ({{ $invoice->electricity_old_reading }} - {{ $invoice->electricity_new_reading }} kWh): {{ number_format($invoice->electricity_amount) }} VNĐ</li>
            @endif
            @if($invoice->water_amount > 0)
            <li>Nước ({{ $invoice->water_old_reading }} - {{ $invoice->water_new_reading }} m³): {{ number_format($invoice->water_amount) }} VNĐ</li>
            @endif
            @if($invoice->wifi_amount > 0)
            <li>Wifi: {{ number_format($invoice->wifi_amount) }} VNĐ</li>
            @endif
            @if($invoice->trash_amount > 0)
            <li>Rác: {{ number_format($invoice->trash_amount) }} VNĐ</li>
            @endif
            @if($invoice->other_services_amount > 0)
            <li>Dịch vụ khác: {{ number_format($invoice->other_services_amount) }} VNĐ</li>
            @endif
        </ul>

        <p>Vui lòng thanh toán đúng hạn để tránh các khoản phí phát sinh.</p>
        
        <p>Trân trọng,<br>
        <strong>{{ $invoice->landlord->name }}</strong></p>
    </div>
</body>
</html>

