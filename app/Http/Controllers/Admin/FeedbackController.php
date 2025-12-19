<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{

    public function index(Request $request)
    {
        $query = Feedback::with('user');

        if ($request->filled('is_resolved')) {
            $query->where('is_resolved', $request->is_resolved == '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('subject', 'like', '%' . $search . '%')
                  ->orWhere('message', 'like', '%' . $search . '%');
            });
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.feedback.index', compact('feedbacks'));
    }

    public function show($id)
    {
        $feedback = Feedback::with('user')->findOrFail($id);
        return view('admin.feedback.show', compact('feedback'));
    }

    public function markProcessed($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'is_resolved' => true,
            'resolved_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Phản hồi đã được đánh dấu đã xử lý.');
    }

    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
            ->with('success', 'Phản hồi đã được xóa thành công.');
    }
}
