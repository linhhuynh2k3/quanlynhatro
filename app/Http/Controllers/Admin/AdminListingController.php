<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use Illuminate\Http\Request;

class AdminListingController extends Controller
{

    public function index(Request $request)
    {
        $query = Listing::with(['user', 'category']);

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.listings.index', compact('listings'));
    }

    public function show($id)
    {
        $listing = Listing::with(['user', 'category', 'comments.user'])->findOrFail($id);
        return view('admin.listings.show', compact('listing'));
    }

    public function approve($id)
    {
        $listing = Listing::with('user')->findOrFail($id);
        
        // Nếu đã thanh toán rồi thì không trừ tiền nữa
        if ($listing->is_paid) {
            $listing->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
            
            return redirect()->back()
                ->with('success', 'Bài đăng đã được duyệt thành công.');
        }
        
        // Tính giá đăng tin
        $listingPrice = \App\Helpers\ListingPriceHelper::calculateRegularPrice(
            $listing->payment_type ?? 'daily',
            $listing->duration_days ?? 30
        );
        
        $user = $listing->user;
        
        // Kiểm tra số dư
        if ($user->balance < $listingPrice) {
            return redirect()->back()
                ->with('error', 'Chủ trọ không đủ số dư để thanh toán. Số dư hiện tại: ' . number_format($user->balance) . ' VNĐ. Cần: ' . number_format($listingPrice) . ' VNĐ.');
        }
        
        // Trừ tiền
        $user->decrement('balance', $listingPrice);
        
        // Cập nhật expired_at dựa trên duration_days
        $expiredAt = now()->addDays($listing->duration_days ?? 30);
        
        // Tạo payment record
        \App\Models\Payment::create([
            'user_id' => $user->id,
            'type' => 'listing_payment',
            'amount' => $listingPrice,
            'status' => 'success',
            'method' => 'wallet',
            'description' => "Thanh toán đăng tin #{$listing->id} - {$listing->title}",
            'listing_id' => $listing->id,
        ]);
        
        // Cập nhật listing
        $listing->update([
            'status' => 'approved',
            'approved_at' => now(),
            'expired_at' => $expiredAt,
            'listing_price' => $listingPrice,
            'is_paid' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Bài đăng đã được duyệt và thanh toán thành công. Số tiền: ' . number_format($listingPrice) . ' VNĐ.');
    }

    public function reject($id)
    {
        $listing = Listing::findOrFail($id);
        
        $listing->update([
            'status' => 'rejected',
        ]);

        return redirect()->back()
            ->with('success', 'Bài đăng đã bị từ chối.');
    }

    public function expire($id)
    {
        $listing = Listing::findOrFail($id);
        
        $listing->update([
            'status' => 'expired',
            'expired_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Bài đăng đã được đánh dấu hết hạn.');
    }

    public function destroy($id)
    {
        // Chỉ super admin mới có quyền xóa
        if (!auth()->user()->canDelete()) {
            abort(403, 'Bạn không có quyền xóa bài đăng.');
        }

        $listing = Listing::findOrFail($id);
        $listing->delete();

        return redirect()->route('admin.listings.index')
            ->with('success', 'Bài đăng đã được xóa.');
    }
}
