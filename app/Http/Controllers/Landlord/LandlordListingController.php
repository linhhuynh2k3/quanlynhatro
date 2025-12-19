<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\Listing;
use App\Models\Category;
use App\Models\User;
use App\Services\ContentModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\StorageHelper;

class LandlordListingController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Listing::where('user_id', $user->id);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('landlord.listings.index', compact('listings'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('landlord.listings.create', compact('categories'));
    }

    public function store(StoreListingRequest $request)
    {
        $validated = $request->validated();
        
        // Kiểm duyệt nội dung
        if (config('moderation.enabled', true)) {
            $moderationService = app(ContentModerationService::class);
            $moderationResult = $moderationService->checkListing(
                $validated['title'],
                $validated['description']
            );
            
            if ($moderationResult['is_violated']) {
                $action = config('moderation.action_on_violation', 'reject');
                
                if ($action === 'reject') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Nội dung bài đăng không phù hợp: ' . $moderationResult['reason']);
                } elseif ($action === 'flag') {
                    // Đánh dấu cần duyệt kỹ hơn
                    $validated['status'] = 'pending';
                }
            }
        }
        
        $validated['user_id'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'pending'; // Chờ admin duyệt
        $validated['available_units'] = $validated['total_units'];
        
        // Thiết lập duration và payment type (mặc định: daily, 30 ngày)
        $validated['duration_days'] = $request->input('duration_days', 30);
        $validated['payment_type'] = $request->input('payment_type', 'daily');
        $validated['is_paid'] = false; // Chưa thanh toán, sẽ trừ khi được duyệt

        // Upload images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = StorageHelper::storeAndCopy($image, 'listings');
            }
            $validated['images'] = json_encode($imagePaths);
        }

        $listing = Listing::create($validated);

        return redirect()->route('landlord.listings.index')
            ->with('success', 'Bài đăng đã được tạo và đang chờ duyệt.');
    }

    public function show($id)
    {
        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);
        return view('landlord.listings.show', compact('listing'));
    }

    public function edit($id)
    {
        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('landlord.listings.edit', compact('listing', 'categories'));
    }

    public function update(UpdateListingRequest $request, $id)
    {
        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);

        // Cho phép sửa bài đăng bất kỳ trạng thái nào
        // Kiểm tra xem bài đăng đã thanh toán và còn hạn không
        $isPaidAndActive = $listing->status === 'approved' && 
                           $listing->is_paid && 
                           $listing->expired_at && 
                           $listing->expired_at > now();
        
        $validated = $request->validated();

        // Kiểm duyệt nội dung
        if (config('moderation.enabled', true)) {
            $moderationService = app(ContentModerationService::class);
            $moderationResult = $moderationService->checkListing(
                $validated['title'],
                $validated['description']
            );
            
            if ($moderationResult['is_violated']) {
                $action = config('moderation.action_on_violation', 'reject');
                
                if ($action === 'reject') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Nội dung bài đăng không phù hợp: ' . $moderationResult['reason']);
                } elseif ($action === 'flag') {
                    // Đánh dấu cần duyệt kỹ hơn
                    $validated['status'] = 'pending';
                }
            }
        }

        // Upload images mới
        if ($request->hasFile('images')) {
            // Xóa ảnh cũ
            $oldImages = json_decode($listing->images ?? '[]', true);
            foreach ($oldImages as $oldImage) {
                StorageHelper::deleteFromBoth($oldImage);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = StorageHelper::storeAndCopy($image, 'listings');
            }
            $validated['images'] = json_encode($imagePaths);
        }

        if (isset($validated['total_units'])) {
            $delta = $validated['total_units'] - $listing->total_units;
            $newAvailable = max(0, $listing->available_units + $delta);
            $validated['available_units'] = min($validated['total_units'], $newAvailable);
        }

        // Logic xử lý status sau khi sửa:
        // - Nếu bài đăng đã thanh toán và còn hạn: Giữ nguyên status = 'approved' (không cần duyệt lại)
        // - Nếu bài đăng đã thanh toán nhưng hết hạn: Reset về pending để duyệt lại
        // - Nếu bài đăng chưa thanh toán: Reset về pending
        if ($isPaidAndActive) {
            // Bài đăng đã thanh toán và còn hạn, giữ nguyên status approved
            // Không cần reset về pending, không cần thanh toán lại
            $validated['status'] = 'approved';
            // Giữ nguyên is_paid và listing_price
        } else {
            // Bài đăng chưa thanh toán hoặc đã hết hạn, reset về pending để admin duyệt lại
            $validated['status'] = 'pending';
            $validated['approved_at'] = null;
            // Nếu đã thanh toán nhưng hết hạn, giữ nguyên is_paid và listing_price
            // Nếu chưa thanh toán, giữ nguyên is_paid = false
        }

        $listing->update($validated);

        return redirect()->route('landlord.listings.index')
            ->with('success', 'Bài đăng đã được cập nhật và đang chờ duyệt lại.');
    }

    public function destroy($id)
    {
        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);

        // Xóa ảnh
        $images = json_decode($listing->images ?? '[]', true);
        foreach ($images as $image) {
            StorageHelper::deleteFromBoth($image);
        }

        $listing->delete();

        return redirect()->route('landlord.listings.index')
            ->with('success', 'Bài đăng đã được xóa.');
    }

    public function extend(Request $request, $id)
    {
        $listing = Listing::where('user_id', Auth::id())->findOrFail($id);
        /** @var User $user */
        $user = Auth::user();

        // Phí gia hạn (ví dụ: 50,000 VNĐ)
        $extensionFee = 50000;
        $extensionDays = $request->input('days', 30);

        if ($user->balance < $extensionFee) {
            return redirect()->back()
                ->with('error', 'Số dư không đủ. Vui lòng nạp tiền vào tài khoản.');
        }

        // Trừ tiền (sử dụng decrement như trong LandlordPaymentController)
        $user->decrement('balance', $extensionFee);

        // Gia hạn
        $newExpiredAt = $listing->expired_at 
            ? $listing->expired_at->copy()->addDays($extensionDays)
            : now()->addDays($extensionDays);
        
        $listing->update([
            'expired_at' => $newExpiredAt
        ]);

        // Tạo payment record
        \App\Models\Payment::create([
            'user_id' => $user->id,
            'type' => 'listing_payment',
            'amount' => $extensionFee,
            'status' => 'success',
            'method' => 'wallet',
            'description' => "Gia hạn bài đăng #{$listing->id} thêm {$extensionDays} ngày",
            'listing_id' => $listing->id,
        ]);

        return redirect()->back()
            ->with('success', "Bài đăng đã được gia hạn thêm {$extensionDays} ngày.");
    }
}
