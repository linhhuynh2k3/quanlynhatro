<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:tenant']);
    }

    public function pay(Request $request, Contract $contract): RedirectResponse
    {
        abort_if($contract->tenant_id !== $request->user()->id, 403);
        $contract->loadMissing('listing');

        if ($contract->approval_status !== 'approved') {
            return back()->with('error', 'Chủ trọ chưa duyệt yêu cầu này.');
        }

        if ($contract->payment_status === 'paid') {
            return redirect()->back()->with('error', 'Hợp đồng này đã được thanh toán.');
        }

        if ($contract->status === 'cancelled') {
            return redirect()->back()->with('error', 'Hợp đồng này đã bị hủy.');
        }

        if (!$contract->listing) {
            return back()->with('error', 'Không tìm thấy bài đăng.');
        }

        if ($contract->listing->available_units < 1) {
            return back()->with('error', 'Bài đăng hiện không còn phòng trống.');
        }

        $request->validate([
            'bank_code' => 'nullable|string|max:20',
        ]);

        if (!config('vnpay.tmn_code') || !config('vnpay.hash_secret')) {
            return redirect()->back()->with('error', 'VNPAY chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
        }

        $user = $request->user();
        $amount = $contract->monthly_price;

        $payment = Payment::create([
            'user_id' => $user->id,
            'credited_user_id' => $contract->landlord_id,
            'type' => 'booking_payment',
            'amount' => $amount,
            'status' => 'pending',
            'method' => 'vnpay',
            'description' => "Thanh toán hợp đồng #{$contract->id}",
            'contract_id' => $contract->id,
            'meta' => [
                'contract_id' => $contract->id,
                'ip' => $request->ip(),
            ],
        ]);

        $transactionCode = 'TN' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) . strtoupper(Str::random(4));
        $payment->update(['transaction_code' => $transactionCode]);

        $contract->update([
            'payment_status' => 'processing',
            'payment_id' => $payment->id,
        ]);

        $params = $this->buildVnpayParams($contract, $transactionCode, $amount, $request, $request->bank_code);
        $paymentUrl = $this->generateVnpayUrl($params);

        return redirect()->away($paymentUrl);
    }

    protected function buildVnpayParams(Contract $contract, string $transactionCode, float $amount, Request $request, ?string $bankCode = null): array
    {
        $now = now('Asia/Ho_Chi_Minh');

        $params = [
            'vnp_Version' => config('vnpay.version'),
            'vnp_TmnCode' => config('vnpay.tmn_code'),
            'vnp_Amount' => (int) ($amount * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_CurrCode' => config('vnpay.curr_code'),
            'vnp_IpAddr' => $request->ip(),
            'vnp_Locale' => config('vnpay.locale'),
            'vnp_OrderInfo' => 'Thanh toan hop dong thue #' . $contract->id,
            'vnp_OrderType' => config('vnpay.order_type'),
            'vnp_ReturnUrl' => config('vnpay.return_url_tenant', config('vnpay.return_url_landlord')),
            'vnp_TxnRef' => $transactionCode,
        ];

        $expireMinutes = (int) config('vnpay.expire_minutes', 15);
        if ($expireMinutes > 0) {
            $params['vnp_ExpireDate'] = $now->copy()->addMinutes($expireMinutes)->format('YmdHis');
        }

        if ($bankCode) {
            $params['vnp_BankCode'] = $bankCode;
        }

        return $params;
    }

    protected function generateVnpayUrl(array $params): string
    {
        ksort($params);

        $query = [];
        foreach ($params as $key => $value) {
            $query[] = urlencode($key) . '=' . urlencode($value);
        }

        $hashData = implode('&', $query);
        $secureHash = hash_hmac('sha512', $hashData, config('vnpay.hash_secret'));

        return config('vnpay.url') . '?' . $hashData . '&vnp_SecureHash=' . $secureHash;
    }
}

