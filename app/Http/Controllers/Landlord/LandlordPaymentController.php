<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LandlordPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:landlord'])->except(['vnpayReturn', 'tenantVnpayReturn', 'vnpayIpn']);
    }

    public function index(): View
    {
        $user = auth()->user();

        return view('landlord.payments.index', [
            'balance' => $user->balance,
        ]);
    }

    public function deposit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000',
            'description' => 'nullable|string|max:500',
            'bank_code' => 'nullable|string|max:20',
        ]);

        if (!config('vnpay.tmn_code') || !config('vnpay.hash_secret')) {
            return redirect()->back()->with('error', 'VNPAY chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
        }

        $user = $request->user();

        $payment = Payment::create([
            'user_id' => $user->id,
            'credited_user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'status' => 'pending',
            'method' => 'vnpay',
            'description' => $validated['description'] ?? 'Nạp tiền qua VNPAY',
            'meta' => [
                'description' => $validated['description'] ?? null,
                'ip' => $request->ip(),
            ],
        ]);

        $transactionCode = 'LD' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) . strtoupper(Str::random(4));
        $payment->update(['transaction_code' => $transactionCode]);

        $params = $this->buildVnpayParams($transactionCode, $validated['amount'], $request, $validated['bank_code'] ?? null);
        $paymentUrl = $this->generateVnpayUrl($params);

        return redirect()->away($paymentUrl);
    }

    public function payListing(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'listing_id' => 'required|exists:listings,id',
        ]);

        $user = $request->user();
        $listing = Listing::findOrFail($validated['listing_id']);

        if ($listing->user_id !== $user->id) {
            return redirect()->back()->with('error', 'Bạn không có quyền thanh toán bài đăng này.');
        }

        $listingFee = 100000;

        if ($user->balance < $listingFee) {
            return redirect()->back()->with('error', 'Số dư không đủ. Vui lòng nạp tiền vào tài khoản.');
        }

        $user->decrement('balance', $listingFee);

        Payment::create([
            'user_id' => $user->id,
            'type' => 'listing_payment',
            'amount' => $listingFee,
            'status' => 'success',
            'method' => 'wallet',
            'description' => "Thanh toán phí đăng bài #{$listing->id}",
            'listing_id' => $listing->id,
        ]);

        if ($listing->status === 'approved') {
            $listing->update([
                'expired_at' => optional($listing->expired_at)->addDays(30) ?? now()->addDays(30),
            ]);
        }

        return redirect()->back()->with('success', 'Đã thanh toán phí đăng bài thành công.');
    }

    public function history(Request $request): View
    {
        $user = $request->user();
        
        $query = Payment::where('user_id', $user->id);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('landlord.payments.history', compact('payments'));
    }

    public function vnpayReturn(Request $request): RedirectResponse
    {
        $result = $this->processVnpayResponse($request->all());
        $messageKey = $result['success'] ? 'success' : 'error';

        return redirect()->route('landlord.payments.index')->with($messageKey, $result['message']);
    }

    public function tenantVnpayReturn(Request $request): RedirectResponse
    {
        $result = $this->processVnpayResponse($request->all());
        $messageKey = $result['success'] ? 'success' : 'error';

        return redirect()->route('bookings.index')->with($messageKey, $result['message']);
    }

    public function vnpayIpn(Request $request): JsonResponse
    {
        $result = $this->processVnpayResponse($request->all());

        return response()->json([
            'RspCode' => $result['rsp_code'],
            'Message' => $result['message'],
        ]);
    }

    protected function buildVnpayParams(string $transactionCode, float $amount, Request $request, ?string $bankCode = null): array
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
            'vnp_OrderInfo' => 'Nap tien tai khoan chu tro #' . $request->user()->id,
            'vnp_OrderType' => config('vnpay.order_type'),
            'vnp_ReturnUrl' => config('vnpay.return_url_landlord'),
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
            ->with(['user', 'contract.listing'])
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

            $creditedUser = $payment->creditedUser ?? $payment->user;
            if ($creditedUser) {
                $creditedUser->increment('balance', $payment->amount);
            }

            if ($payment->type === 'booking_payment' && $payment->contract) {
                $contractUpdate = [
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'payment_id' => $payment->id,
                ];

                if (!$payment->contract->reserved_at && $payment->contract->listing) {
                    if ($payment->contract->listing->available_units > 0) {
                        $payment->contract->listing->decrement('available_units');
                        $contractUpdate['reserved_at'] = now();
                    }
                }

                $payment->contract->update($contractUpdate);
            }
        } elseif (!$isSuccess && $payment->status === 'pending') {
            $payment->status = 'failed';
            $payment->save();

            if ($payment->type === 'booking_payment' && $payment->contract) {
                $contractUpdate = [
                    'payment_status' => 'failed',
                ];

                if ($payment->contract->reserved_at && $payment->contract->listing) {
                    $payment->contract->listing->increment('available_units');
                    $contractUpdate['reserved_at'] = null;
                }

                $payment->contract->update($contractUpdate);
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
