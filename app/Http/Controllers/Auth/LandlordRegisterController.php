<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LandlordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class LandlordRegisterController extends Controller
{
    /**
     * Display the landlord registration view.
     */
    public function create(): View
    {
        return view('auth.landlord-register');
    }

    /**
     * Handle an incoming landlord registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'unique:landlord_requests,email'],
            'phone' => ['required', 'string', 'max:20'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:1000'],
            'cccd_number' => ['required', 'string', 'max:20'],
            'cccd_front_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'cccd_back_image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'business_license_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Upload ảnh CCCD mặt trước
        $cccdFrontPath = \App\Helpers\StorageHelper::storeAndCopy($request->file('cccd_front_image'), 'landlord-requests/cccd');
        
        // Upload ảnh CCCD mặt sau
        $cccdBackPath = \App\Helpers\StorageHelper::storeAndCopy($request->file('cccd_back_image'), 'landlord-requests/cccd');
        
        // Upload giấy phép kinh doanh (nếu có)
        $businessLicensePath = null;
        if ($request->hasFile('business_license_image')) {
            $businessLicensePath = \App\Helpers\StorageHelper::storeAndCopy($request->file('business_license_image'), 'landlord-requests/business-license');
        }

        // Tạo yêu cầu đăng ký chủ trọ
        LandlordRequest::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'message' => $request->message,
            'cccd_number' => $request->cccd_number,
            'cccd_front_image' => $cccdFrontPath,
            'cccd_back_image' => $cccdBackPath,
            'business_license_image' => $businessLicensePath,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Yêu cầu đăng ký làm chủ trọ của bạn đã được gửi. Vui lòng chờ quản trị viên duyệt. Bạn sẽ nhận được thông báo qua email khi được duyệt.');
    }
}
