<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::with('parent')->orderBy('position')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được tạo thành công.');
    }

    public function show($id)
    {
        $category = Category::with(['parent', 'children', 'listings'])->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
            'is_active' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = \Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Kiểm tra xem có danh mục con không
        if ($category->children()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Không thể xóa danh mục có danh mục con.');
        }

        // Kiểm tra xem có bài đăng không
        if ($category->listings()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Không thể xóa danh mục có bài đăng.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Danh mục đã được xóa thành công.');
    }
}
