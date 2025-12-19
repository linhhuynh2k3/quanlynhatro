<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Listing;
use App\Services\ContentModerationService;
use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hiển thị danh sách cuộc trò chuyện
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isTenant()) {
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
            
            return view('tenant.chat.index', compact('conversations'));
        } elseif ($user->isAdmin() || $user->isAgent()) {
            // Admin/Agent: Xem tất cả conversations
            $conversations = Conversation::with(['tenant', 'landlord', 'listing', 'messages' => function($q) {
                    $q->latest()->limit(1);
                }])
                ->orderBy('last_message_at', 'desc')
                ->get()
                ->map(function($conv) use ($user) {
                    $conv->unread_count = $conv->unreadCount($user->id);
                    return $conv;
                });
            
            return view('admin.chat.index', compact('conversations'));
        } else {
            // Landlord
            $conversations = Conversation::where('landlord_id', $user->id)
                ->with(['tenant', 'listing', 'messages' => function($q) {
                    $q->latest()->limit(1);
                }])
                ->orderBy('last_message_at', 'desc')
                ->get()
                ->map(function($conv) use ($user) {
                    $conv->unread_count = $conv->unreadCount($user->id);
                    return $conv;
                });
            
            return view('landlord.chat.index', compact('conversations'));
        }
    }

    /**
     * Tạo hoặc lấy cuộc trò chuyện với chủ trọ
     */
    public function startConversation(Request $request, $listingId)
    {
        $user = auth()->user();
        
        if (!$user->isTenant()) {
            abort(403, 'Chỉ người thuê mới có thể bắt đầu cuộc trò chuyện.');
        }

        $listing = Listing::findOrFail($listingId);
        
        // Tìm hoặc tạo conversation
        $conversation = Conversation::firstOrCreate(
            [
                'tenant_id' => $user->id,
                'landlord_id' => $listing->user_id,
                'listing_id' => $listingId,
            ],
            [
                'last_message_at' => now(),
            ]
        );

        return redirect()->route('chat.show', $conversation->id);
    }

    /**
     * Hiển thị cuộc trò chuyện
     */
    public function show($conversationId)
    {
        $user = auth()->user();
        
        if (!$user) {
            abort(401, 'Unauthorized');
        }
        
        $conversation = Conversation::with(['tenant', 'landlord', 'listing'])
            ->findOrFail($conversationId);

        // Kiểm tra quyền truy cập
        if ($user->isTenant() && $conversation->tenant_id !== $user->id) {
            abort(403);
        }
        if ($user->isLandlord() && $conversation->landlord_id !== $user->id) {
            abort(403);
        }
        // Admin/Agent có thể xem tất cả conversations
        
        // Lấy tin nhắn
        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Đánh dấu tin nhắn đã đọc
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Sử dụng view khác nhau cho tenant, landlord và admin
        // QUAN TRỌNG: Kiểm tra role một cách rõ ràng và ưu tiên landlord
        $userRole = $user->role;
        
        // Debug log
        Log::info('ChatController@show - Role check', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'user_role_type' => gettype($userRole),
            'isLandlord_method' => $user->isLandlord(),
            'role_equals_landlord' => ($userRole === 'landlord'),
            'role_equals_landlord_strict' => ($userRole === 'landlord'),
            'conversation_id' => $conversationId,
            'conversation_landlord_id' => $conversation->landlord_id,
        ]);
        
        // Kiểm tra landlord TRƯỚC TIÊN - đây là điều kiện quan trọng nhất
        if ($userRole === 'landlord') {
            Log::info('ChatController@show - Returning landlord.chat.show for landlord');
            return view('landlord.chat.show', compact('conversation', 'messages'));
        }
        
        // Kiểm tra bằng method isLandlord() như backup
        if ($user->isLandlord()) {
            Log::info('ChatController@show - Returning landlord.chat.show via isLandlord() method');
            return view('landlord.chat.show', compact('conversation', 'messages'));
        }
        
        if ($user->isAdmin() || $user->isAgent()) {
            // Admin/Agent: Nếu conversation có landlord thì dùng landlord view
            if ($conversation->landlord_id) {
                Log::info('ChatController@show - Returning landlord.chat.show for admin/agent');
                return view('landlord.chat.show', compact('conversation', 'messages'));
            }
            // Nếu không có landlord (có thể là conversation giữa tenant và admin)
            Log::info('ChatController@show - Returning tenant.chat.show for admin/agent');
            return view('tenant.chat.show', compact('conversation', 'messages'));
        }
        
        // Mặc định là tenant
        Log::info('ChatController@show - Returning tenant.chat.show (default)', [
            'user_id' => $user->id,
            'user_role' => $userRole
        ]);
        return view('tenant.chat.show', compact('conversation', 'messages'));
    }

    /**
     * Gửi tin nhắn
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $user = auth()->user();
        
        $conversation = Conversation::findOrFail($conversationId);

        // Kiểm tra quyền
        if ($user->isTenant() && $conversation->tenant_id !== $user->id) {
            abort(403);
        }
        if ($user->isLandlord() && $conversation->landlord_id !== $user->id) {
            abort(403);
        }
        // Admin/Agent có thể gửi tin nhắn trong bất kỳ conversation nào

        $validated = $request->validate([
            'message' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:10240', // Max 10MB
        ]);

        // Phải có ít nhất message hoặc file
        if (empty($validated['message']) && !$request->hasFile('file')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Vui lòng nhập tin nhắn hoặc chọn file để gửi.'
                ], 422);
            }
            return redirect()->back()
                ->with('error', 'Vui lòng nhập tin nhắn hoặc chọn file để gửi.');
        }

        // Kiểm duyệt nội dung tin nhắn (chỉ khi có message text)
        if (!empty($validated['message']) && config('moderation.enabled', true)) {
            $moderationService = app(ContentModerationService::class);
            $moderationResult = $moderationService->checkText($validated['message']);
            
            if ($moderationResult['is_violated']) {
                $action = config('moderation.action_on_violation', 'reject');
                
                if ($action === 'reject') {
                    // Luôn trả về JSON nếu request có Accept: application/json
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Nội dung tin nhắn không phù hợp: ' . $moderationResult['reason']
                        ], 422);
                    }
                    return redirect()->back()
                        ->with('error', 'Nội dung tin nhắn không phù hợp: ' . $moderationResult['reason']);
                }
            }
        }

        // Xử lý file upload
        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            
            // Xác định loại file
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $fileType = 'image';
                $filePath = StorageHelper::storeAndCopy($file, 'chat/images');
            } else {
                $fileType = 'document';
                $filePath = StorageHelper::storeAndCopy($file, 'chat/files');
            }
        }

        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $user->id,
            'message' => $validated['message'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        // Cập nhật last_message_at
        $conversation->update(['last_message_at' => now()]);

        // Luôn trả về JSON nếu request có Accept: application/json
        if ($request->wantsJson() || $request->ajax()) {
            $message->load('sender');
            // Đảm bảo file_path là URL đầy đủ
            if ($message->file_path) {
                $message->file_path = \App\Helpers\ImageHelper::url($message->file_path);
            }
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', 'Tin nhắn đã được gửi.');
    }

    /**
     * API để lấy tin nhắn mới (cho real-time)
     */
    public function getMessages(Request $request, $conversationId)
    {
        $user = auth()->user();
        
        $conversation = Conversation::findOrFail($conversationId);

        // Kiểm tra quyền
        if ($user->isTenant() && $conversation->tenant_id !== $user->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }
            abort(403);
        }
        if ($user->isLandlord() && $conversation->landlord_id !== $user->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $lastMessageId = $request->get('last_message_id', 0);

        $messages = Message::where('conversation_id', $conversationId)
            ->where('id', '>', $lastMessageId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Đánh dấu đã đọc
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->where('id', '>', $lastMessageId)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Đảm bảo file_path là URL đầy đủ cho tất cả messages
        $messages->transform(function($msg) {
            if ($msg->file_path) {
                $msg->file_path = \App\Helpers\ImageHelper::url($msg->file_path);
            }
            return $msg;
        });

        // Luôn trả về JSON cho API requests
        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}
