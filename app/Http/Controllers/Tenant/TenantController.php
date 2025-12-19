<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Conversation;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:tenant');
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        // Lấy các hợp đồng đã thuê
        $contracts = Contract::where('tenant_id', $user->id)
            ->with(['listing', 'landlord', 'listing.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Lấy các cuộc trò chuyện
        $conversations = Conversation::where('tenant_id', $user->id)
            ->with(['landlord', 'listing', 'messages' => function($q) {
                $q->latest()->limit(1);
            }])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function($conv) use ($user) {
                $conv->unread_count = $conv->unreadCount($user->id);
                return $conv;
            });

        // Thống kê
        $stats = [
            'total_contracts' => Contract::where('tenant_id', $user->id)->count(),
            'active_contracts' => Contract::where('tenant_id', $user->id)
                ->where('status', 'active')->count(),
            'pending_contracts' => Contract::where('tenant_id', $user->id)
                ->where('approval_status', 'pending')->count(),
            'unread_messages' => Conversation::where('tenant_id', $user->id)
                ->get()
                ->sum(function($conv) use ($user) {
                    return $conv->unreadCount($user->id);
                }),
        ];

        return view('tenant.dashboard', compact('contracts', 'conversations', 'stats'));
    }
}
