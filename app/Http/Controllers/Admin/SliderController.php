<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class SliderController extends Controller
{

    public function index()
    {
        $sliders = Slider::orderBy('position')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = StorageHelper::storeAndCopy($request->file('image'), 'sliders');
        }

        Slider::create($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider đã được tạo thành công.');
    }

    public function show($id)
    {
        $slider = Slider::findOrFail($id);
        return view('admin.sliders.show', compact('slider'));
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'link' => 'nullable|url|max:500',
            'is_active' => 'boolean',
            'position' => 'nullable|integer',
        ]);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            if ($slider->image) {
                StorageHelper::deleteFromBoth($slider->image);
            }
            $validated['image'] = StorageHelper::storeAndCopy($request->file('image'), 'sliders');
        } else {
            unset($validated['image']);
        }

        $slider->update($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider đã được cập nhật thành công.');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);

        // Xóa ảnh
        if ($slider->image) {
            StorageHelper::deleteFromBoth($slider->image);
        }

        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider đã được xóa thành công.');
    }
}
