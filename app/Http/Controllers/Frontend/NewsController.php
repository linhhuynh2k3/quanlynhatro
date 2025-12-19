<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = News::with('author')
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $news = $query->paginate(12);

        return view('frontend.news.index', compact('news'));
    }

    public function show($slug)
    {
        $news = News::with('author')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // Lấy tin tức liên quan (cùng tác giả hoặc cùng thời gian)
        $relatedNews = News::with('author')
            ->where('is_published', true)
            ->where('id', '!=', $news->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return view('frontend.news.show', compact('news', 'relatedNews'));
    }
}
