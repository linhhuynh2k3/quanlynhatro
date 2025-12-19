<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo hóa đơn tiền phòng</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">Hóa đơn tiền phòng</h1>
        <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">Số hóa đơn: <strong>{{ $bill->bill_number }}</strong></p>
    </div>

    <div style="background: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none;">
        <p>Xin chào <strong>{{ $bill->tenant->name }}</strong>,</p>
        
        <p>Chủ trọ đã tạo hóa đơn tiền phòng cho bạn. Vui lòng xem chi tiết và thanh toán trước hạn.</p>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            <h3 style="margin-top: 0; color: #667eea;">Thông tin hóa đơn</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0;"><strong>Phòng trọ:</strong></td>
                    <td style="padding: 8px 0;">{{ $bill->listing->title }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Địa chỉ:</strong></td>
                    <td style="padding: 8px 0;">{{ $bill->listing->address }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Ngày lập:</strong></td>
                    <td style="padding: 8px 0;">{{ $bill->bill_date->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Hạn thanh toán:</strong></td>
                    <td style="padding: 8px 0; color: #e74c3c;"><strong>{{ $bill->due_date->format('d/m/Y') }}</strong></td>
                </tr>
            </table>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Chi tiết hóa đơn</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0;">Tiền phòng:</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($bill->room_price, 0, ',', '.') }}₫</td>
                </tr>
                @if($bill->electricity_usage)
                <tr>
                    <td style="padding: 8px 0;">Tiền điện ({{ $bill->electricity_usage }} kWh):</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($bill->electricity_total, 0, ',', '.') }}₫</td>
                </tr>
                @endif
                @if($bill->water_usage)
                <tr>
                    <td style="padding: 8px 0;">Tiền nước ({{ $bill->water_usage }} m³):</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($bill->water_total, 0, ',', '.') }}₫</td>
                </tr>
                @endif
                @if($bill->wifi_price > 0)
                <tr>
                    <td style="padding: 8px 0;">Tiền Wifi:</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($bill->wifi_price, 0, ',', '.') }}₫</td>
                </tr>
                @endif
                @if($bill->garbage_price > 0)
                <tr>
                    <td style="padding: 8px 0;">Tiền rác:</td>
                    <td style="padding: 8px 0; text-align: right;">{{ number_format($bill->garbage_price, 0, ',', '.') }}₫</td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #333;">
                    <td style="padding: 12px 0;"><strong>Tổng cộng:</strong></td>
                    <td style="padding: 12px 0; text-align: right; font-size: 18px; color: #e74c3c;"><strong>{{ number_format($bill->total_amount, 0, ',', '.') }}₫</strong></td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $viewUrl }}" 
               style="display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">
                Xem chi tiết và thanh toán
            </a>
        </div>

        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            <strong>Lưu ý:</strong> Vui lòng thanh toán trước hạn để tránh phát sinh phí trễ hạn.
        </p>

        <p style="color: #666; font-size: 14px; margin-top: 20px;">
            Nếu bạn có thắc mắc, vui lòng liên hệ với chủ trọ qua hệ thống chat hoặc email.
        </p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 30px 0;">

        <p style="color: #999; font-size: 12px; text-align: center; margin: 0;">
            Email này được gửi tự động từ hệ thống Homestay.com<br>
            Vui lòng không trả lời email này.
        </p>
    </div>
</body>
</html>

