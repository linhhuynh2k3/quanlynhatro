<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Listing;
use App\Services\ContentModerationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except([]);
    }

    public function store(Request $request, $listingId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Kiểm duyệt nội dung
        if (config('moderation.enabled', true)) {
            $moderationService = app(ContentModerationService::class);
            $moderationResult = $moderationService->checkText($validated['content']);
            
            if ($moderationResult['is_violated']) {
                $action = config('moderation.action_on_violation', 'reject');
                
                if ($action === 'reject') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Nội dung bình luận không phù hợp: ' . $moderationResult['reason']);
                }
            }
        }

        $listing = Listing::findOrFail($listingId);

        Comment::create([
            'user_id' => auth()->id(),
            'listing_id' => $listing->id,
            'content' => $validated['content'],
            'parent_id' => null,
        ]);

        return redirect()->back()
            ->with('success', 'Bình luận đã được thêm thành công.');
    }

    public function reply(Request $request, $commentId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Kiểm duyệt nội dung
        if (config('moderation.enabled', true)) {
            $moderationService = app(ContentModerationService::class);
            $moderationResult = $moderationService->checkText($validated['content']);
            
            if ($moderationResult['is_violated']) {
                $action = config('moderation.action_on_violation', 'reject');
                
                if ($action === 'reject') {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Nội dung phản hồi không phù hợp: ' . $moderationResult['reason']);
                }
            }
        }

        $parentComment = Comment::findOrFail($commentId);

        Comment::create([
            'user_id' => auth()->id(),
            'listing_id' => $parentComment->listing_id,
            'content' => $validated['content'],
            'parent_id' => $commentId,
        ]);

        return redirect()->back()
            ->with('success', 'Phản hồi đã được thêm thành công.');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Chỉ cho phép xóa comment của chính mình
        if ($comment->user_id !== auth()->id()) {
            return redirect()->back()
                ->with('error', 'Bạn không có quyền xóa bình luận này.');
        }

        // Xóa các reply
        Comment::where('parent_id', $id)->delete();
        
        $comment->delete();

        return redirect()->back()
            ->with('success', 'Bình luận đã được xóa.');
    }
}
