<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class NewsController extends Controller
{

    public function index(Request $request)
    {
        $query = News::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $news = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['title']);
        }

        $validated['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['thumbnail'] = StorageHelper::storeAndCopy($request->file('image'), 'news');
        }

        News::create($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'Tin tức đã được tạo thành công.');
    }

    public function show($id)
    {
        $news = News::with('author')->findOrFail($id);
        return view('admin.news.show', compact('news'));
    }

    public function edit($id)
    {
        $news = News::findOrFail($id);
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news,slug,' . $id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_published' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['title']);
        }

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($news->thumbnail) {
                StorageHelper::deleteFromBoth($news->thumbnail);
            }
            $validated['thumbnail'] = StorageHelper::storeAndCopy($request->file('image'), 'news');
        } else {
            unset($validated['image']);
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'Tin tức đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Xóa ảnh
        if ($news->thumbnail) {
            StorageHelper::deleteFromBoth($news->thumbnail);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Tin tức đã được xóa thành công.');
    }
}
