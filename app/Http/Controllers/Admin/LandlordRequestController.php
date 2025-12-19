<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandlordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandlordRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = LandlordRequest::with('approver');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.landlord-requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LandlordRequest $landlordRequest): View
    {
        $landlordRequest->load('approver');
        return view('admin.landlord-requests.show', compact('landlordRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LandlordRequest $landlordRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LandlordRequest $landlordRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LandlordRequest $landlordRequest)
    {
        //
    }

    /**
     * Approve landlord request
     */
    public function approve(Request $request, LandlordRequest $landlordRequest): RedirectResponse
    {
        // Tạo user mới với role landlord
        $user = User::create([
            'name' => $landlordRequest->name,
            'email' => $landlordRequest->email,
            'password' => $landlordRequest->password, // Đã được hash
            'role' => 'landlord',
            'balance' => 0,
        ]);

        // Cập nhật trạng thái request
        $landlordRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // TODO: Gửi email thông báo cho user

        return redirect()->route('admin.landlord-requests.index')
            ->with('success', 'Đã duyệt yêu cầu đăng ký chủ trọ. Tài khoản đã được tạo.');
    }

    /**
     * Reject landlord request
     */
    public function reject(Request $request, LandlordRequest $landlordRequest): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $landlordRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // TODO: Gửi email thông báo cho user

        return redirect()->route('admin.landlord-requests.index')
            ->with('success', 'Đã từ chối yêu cầu đăng ký chủ trọ.');
    }
}
