<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\UtilityBill;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class UtilityBillController extends Controller
{
    /**
     * Hiển thị hóa đơn cho người thuê (truy cập qua link trong email)
     */
    public function show($billId): View
    {
        $bill = UtilityBill::with(['contract', 'listing', 'landlord', 'tenant'])
            ->findOrFail($billId);

        return view('frontend.utility-bills.show', compact('bill'));
    }

    /**
     * Xử lý thanh toán hóa đơn qua VNPay
     */
    public function pay(Request $request, $billId): RedirectResponse
    {
        $bill = UtilityBill::with(['contract', 'listing', 'landlord', 'tenant'])
            ->findOrFail($billId);

        if ($bill->payment_status === 'paid') {
            return back()->with('error', 'Hóa đơn này đã được thanh toán.');
        }

        if (!config('vnpay.tmn_code') || !config('vnpay.hash_secret')) {
            return redirect()->back()->with('error', 'VNPAY chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
        }

        $request->validate([
            'bank_code' => 'nullable|string|max:20',
        ]);

        // Tạo payment record
        $payment = Payment::create([
            'user_id' => $bill->tenant_id,
            'credited_user_id' => $bill->landlord_id,
            'type' => 'utility_bill_payment',
            'amount' => $bill->total_amount,
            'status' => 'pending',
            'method' => 'vnpay',
            'description' => "Thanh toán hóa đơn tiền phòng #{$bill->bill_number}",
            'meta' => [
                'utility_bill_id' => $bill->id,
                'bill_number' => $bill->bill_number,
                'ip' => $request->ip(),
            ],
        ]);

        $transactionCode = 'UB' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) . strtoupper(Str::random(4));
        $payment->update(['transaction_code' => $transactionCode]);

        // Cập nhật trạng thái hóa đơn
        $bill->update([
            'payment_status' => 'processing',
        ]);

        // Tạo VNPay URL
        $params = $this->buildVnpayParams($bill, $transactionCode, $request, $request->bank_code);
        $paymentUrl = $this->generateVnpayUrl($params);

        return redirect()->away($paymentUrl);
    }

    protected function buildVnpayParams(UtilityBill $bill, string $transactionCode, Request $request, ?string $bankCode = null): array
    {
        $now = now('Asia/Ho_Chi_Minh');

        $params = [
            'vnp_Version' => config('vnpay.version'),
            'vnp_TmnCode' => config('vnpay.tmn_code'),
            'vnp_Amount' => (int) ($bill->total_amount * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_CurrCode' => config('vnpay.curr_code'),
            'vnp_IpAddr' => $request->ip(),
            'vnp_Locale' => config('vnpay.locale'),
            'vnp_OrderInfo' => 'Thanh toan hoa don tien phong #' . $bill->bill_number,
            'vnp_OrderType' => config('vnpay.order_type'),
            'vnp_ReturnUrl' => route('utility-bills.vnpay.return'),
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

    /**
     * Xử lý callback từ VNPay
     */
    public function vnpayReturn(Request $request): RedirectResponse
    {
        $result = $this->processVnpayResponse($request->all());
        $messageKey = $result['success'] ? 'success' : 'error';

        // Lấy bill_id từ payment
        $transactionCode = $request->input('vnp_TxnRef');
        $payment = Payment::where('transaction_code', $transactionCode)->first();
        
        if ($payment && isset($payment->meta['utility_bill_id'])) {
            $billId = $payment->meta['utility_bill_id'];
            return redirect()->route('utility-bills.show', $billId)->with($messageKey, $result['message']);
        }

        return redirect()->route('home')->with($messageKey, $result['message']);
    }

    /**
     * Xử lý IPN từ VNPay
     */
    public function vnpayIpn(Request $request)
    {
        $result = $this->processVnpayResponse($request->all());

        return response()->json([
            'RspCode' => $result['rsp_code'],
            'Message' => $result['message'],
        ]);
    }

    protected function processVnpayResponse(array $input): array
    {
        $secureHash = $input['vnp_SecureHash'] ?? null;
        if (!$secureHash) {
            return [
                'success' => false,
                'message' => 'Thiếu chữ ký xác thực.',
                'rsp_code' => '97',
            ];
        }

        $data = $input;
        unset($data['vnp_SecureHash'], $data['vnp_SecureHashType']);
        ksort($data);

        $hashPieces = [];
        foreach ($data as $key => $value) {
            $hashPieces[] = urlencode($key) . '=' . urlencode($value);
        }
        $calculatedHash = hash_hmac('sha512', implode('&', $hashPieces), config('vnpay.hash_secret'));

        if ($calculatedHash !== $secureHash) {
            return [
                'success' => false,
                'message' => 'Chữ ký không hợp lệ.',
                'rsp_code' => '97',
            ];
        }

        $transactionCode = $input['vnp_TxnRef'] ?? null;
        $payment = Payment::where('transaction_code', $transactionCode)
            ->with(['user'])
            ->first();

        if (!$payment) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy giao dịch.',
                'rsp_code' => '01',
            ];
        }

        $amountFromGateway = (int) ($input['vnp_Amount'] ?? 0) / 100;
        if ((float) $amountFromGateway !== (float) $payment->amount) {
            return [
                'success' => false,
                'message' => 'Sai số tiền.',
                'rsp_code' => '04',
            ];
        }

        $isSuccess = ($input['vnp_ResponseCode'] ?? null) === '00'
            && ($input['vnp_TransactionStatus'] ?? null) === '00';

        $meta = $payment->meta ?? [];
        $meta['vnpay_response'] = $input;
        $payment->meta = $meta;

        if ($isSuccess && $payment->status !== 'success') {
            $payment->status = 'success';
            $payment->method = 'vnpay';
            $payment->save();

            // Cập nhật trạng thái hóa đơn
            if (isset($meta['utility_bill_id'])) {
                $bill = UtilityBill::find($meta['utility_bill_id']);
                if ($bill && $bill->payment_status !== 'paid') {
                    $bill->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }

            // Cộng tiền cho chủ trọ
            $creditedUser = $payment->creditedUser ?? $payment->user;
            if ($creditedUser) {
                $creditedUser->increment('balance', $payment->amount);
            }
        } elseif (!$isSuccess && $payment->status === 'pending') {
            $payment->status = 'failed';
            $payment->save();

            // Cập nhật trạng thái hóa đơn
            if (isset($meta['utility_bill_id'])) {
                $bill = UtilityBill::find($meta['utility_bill_id']);
                if ($bill) {
                    $bill->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }
        } else {
            $payment->save();
        }

        return [
            'success' => $isSuccess,
            'message' => $isSuccess ? 'Thanh toán thành công.' : 'Thanh toán thất bại hoặc bị hủy.',
            'rsp_code' => '00',
        ];
    }
}
