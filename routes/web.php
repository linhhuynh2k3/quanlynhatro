<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminListingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Landlord\LandlordListingController;
use App\Http\Controllers\Landlord\LandlordPaymentController;
use App\Http\Controllers\Frontend\ListingController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\TenantPaymentController;
use App\Http\Controllers\Frontend\CommentController;
use App\Http\Controllers\Frontend\FeedbackController;
use App\Http\Controllers\Frontend\NewsController as FrontendNewsController;
use App\Http\Controllers\Landlord\WithdrawalController as LandlordWithdrawalController;
use App\Http\Controllers\Admin\WithdrawalRequestController as AdminWithdrawalRequestController;
use App\Http\Controllers\Landlord\LandlordBookingController;
use Illuminate\Support\Facades\Route;

// Frontend routes (public)
Route::get('/', [ListingController::class, 'index'])->name('home');
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/map', [ListingController::class, 'map'])->name('listings.map');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/search', [ListingController::class, 'search'])->name('listings.search');

// News routes (public)
Route::get('/news', [FrontendNewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [FrontendNewsController::class, 'show'])->name('news.show');

// Public feedback/contact
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

// Comments (require auth)
Route::middleware('auth')->group(function () {
    Route::post('/listings/{listing}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'reply'])->name('comments.reply');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Booking/Contract (for tenants)
    Route::post('/listings/{listing}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/contracts/{contract}/sign', [BookingController::class, 'sign'])->name('contracts.sign');
    Route::post('/contracts/{contract}/pay', [TenantPaymentController::class, 'pay'])->name('contracts.pay');
});

// Dashboard (redirect based on role)
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin() || $user->isAgent()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isLandlord()) {
        return redirect()->route('landlord.dashboard');
    } elseif ($user->isTenant()) {
        return redirect()->route('tenant.dashboard');
    } else {
        return redirect()->route('home');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('users', UserController::class);
    Route::resource('listings', AdminListingController::class);
    Route::post('/listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('listings.approve');
    Route::post('/listings/{listing}/reject', [AdminListingController::class, 'reject'])->name('listings.reject');
    Route::post('/listings/{listing}/expire', [AdminListingController::class, 'expire'])->name('listings.expire');
    
    Route::resource('categories', CategoryController::class);
    Route::resource('sliders', SliderController::class);
    Route::resource('news', NewsController::class);
    Route::resource('payments', AdminPaymentController::class);
    Route::get('/payments/stats/revenue', [AdminPaymentController::class, 'revenueStats'])->name('payments.revenue');
    Route::resource('feedback', AdminFeedbackController::class);
    Route::post('/feedback/{feedback}/mark-processed', [AdminFeedbackController::class, 'markProcessed'])->name('feedback.mark-processed');
    
    Route::resource('landlord-requests', \App\Http\Controllers\Admin\LandlordRequestController::class);
    Route::post('/landlord-requests/{landlordRequest}/approve', [\App\Http\Controllers\Admin\LandlordRequestController::class, 'approve'])->name('landlord-requests.approve');
    Route::post('/landlord-requests/{landlordRequest}/reject', [\App\Http\Controllers\Admin\LandlordRequestController::class, 'reject'])->name('landlord-requests.reject');

    Route::get('/withdrawals', [AdminWithdrawalRequestController::class, 'index'])->name('withdrawals.index');
    Route::get('/withdrawals/{withdrawal}', [AdminWithdrawalRequestController::class, 'show'])->name('withdrawals.show');
    Route::post('/withdrawals/{withdrawal}/status', [AdminWithdrawalRequestController::class, 'updateStatus'])->name('withdrawals.update-status');
});

// Landlord routes
Route::prefix('landlord')->middleware(['auth', 'role:landlord'])->name('landlord.')->group(function () {
    Route::get('/dashboard', function () {
        return view('landlord.dashboard');
    })->name('dashboard');
    
    Route::resource('listings', LandlordListingController::class);
    Route::post('/listings/{listing}/extend', [LandlordListingController::class, 'extend'])->name('listings.extend');
    
    Route::get('/payments', [LandlordPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/deposit', [LandlordPaymentController::class, 'deposit'])->name('payments.deposit');
    Route::post('/payments/pay-listing', [LandlordPaymentController::class, 'payListing'])->name('payments.pay-listing');
    Route::get('/payments/history', [LandlordPaymentController::class, 'history'])->name('payments.history');

    Route::get('/withdrawals', [LandlordWithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/withdrawals', [LandlordWithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/withdrawals/{withdrawal}/cancel', [LandlordWithdrawalController::class, 'cancel'])->name('withdrawals.cancel');

    Route::get('/bookings', [LandlordBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{contract}', [LandlordBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{contract}/approve', [LandlordBookingController::class, 'approve'])->name('bookings.approve');
    Route::post('/bookings/{contract}/reject', [LandlordBookingController::class, 'reject'])->name('bookings.reject');
    Route::post('/bookings/{contract}/activate', [LandlordBookingController::class, 'activate'])->name('bookings.activate');

    Route::resource('invoices', \App\Http\Controllers\Landlord\InvoiceController::class);
    
    // Quản lý phòng
    Route::get('/rooms', [\App\Http\Controllers\Landlord\RoomManagementController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/{contract}/create-bill', [\App\Http\Controllers\Landlord\RoomManagementController::class, 'createBill'])->name('rooms.create-bill');
    Route::post('/rooms/{contract}/create-bill', [\App\Http\Controllers\Landlord\RoomManagementController::class, 'storeBill'])->name('rooms.store-bill');
    Route::get('/rooms/bills/{bill}', [\App\Http\Controllers\Landlord\RoomManagementController::class, 'showBill'])->name('rooms.show-bill');
});

// Tenant routes
Route::prefix('tenant')->middleware(['auth', 'role:tenant'])->name('tenant.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Tenant\TenantController::class, 'dashboard'])->name('dashboard');
});

// Chat routes (for both tenant and landlord)
Route::middleware('auth')->group(function () {
    Route::get('/chat', [\App\Http\Controllers\Tenant\ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/start/{listingId}', [\App\Http\Controllers\Tenant\ChatController::class, 'startConversation'])->name('chat.start');
    Route::get('/chat/{conversationId}', [\App\Http\Controllers\Tenant\ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversationId}/send', [\App\Http\Controllers\Tenant\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/{conversationId}/messages', [\App\Http\Controllers\Tenant\ChatController::class, 'getMessages'])->name('chat.messages');
});

// Utility bills routes (public - có thể truy cập qua token trong email)
Route::get('/utility-bills/{bill}', [\App\Http\Controllers\Frontend\UtilityBillController::class, 'show'])->name('utility-bills.show');
Route::post('/utility-bills/{bill}/pay', [\App\Http\Controllers\Frontend\UtilityBillController::class, 'pay'])->name('utility-bills.pay');
Route::get('/utility-bills/vnpay/return', [\App\Http\Controllers\Frontend\UtilityBillController::class, 'vnpayReturn'])->name('utility-bills.vnpay.return');
Route::get('/utility-bills/vnpay/ipn', [\App\Http\Controllers\Frontend\UtilityBillController::class, 'vnpayIpn'])->name('utility-bills.vnpay.ipn');

Route::get('/landlord/payments/vnpay/return', [LandlordPaymentController::class, 'vnpayReturn'])->name('landlord.payments.vnpay.return');
Route::get('/landlord/payments/vnpay/ipn', [LandlordPaymentController::class, 'vnpayIpn'])->name('landlord.payments.vnpay.ipn');
Route::get('/bookings/vnpay/return', [LandlordPaymentController::class, 'tenantVnpayReturn'])->name('bookings.vnpay.return');

require __DIR__.'/auth.php';
