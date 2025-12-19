<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isLandlord();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50|max:5000',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:10000|max:100000000',
            'total_units' => 'required|integer|min:1|max:500',
            'area' => 'required|numeric|min:5|max:10000',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'ward' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|regex:/^[0-9]{10,11}$/',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'duration_days' => 'required|integer|min:5|max:365',
            'payment_type' => 'required|in:daily,weekly,monthly',
            'electricity_price' => 'nullable|numeric|min:0',
            'water_price' => 'nullable|numeric|min:0',
            'wifi_price' => 'nullable|numeric|min:0',
            'garbage_price' => 'nullable|numeric|min:0',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề bài đăng.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'description.min' => 'Mô tả phải có ít nhất 50 ký tự.',
            'price.min' => 'Giá phải tối thiểu 10,000 VNĐ.',
            'area.min' => 'Diện tích phải tối thiểu 5 m².',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'images.max' => 'Tối đa 10 ảnh.',
            'images.*.max' => 'Mỗi ảnh tối đa 2MB.',
        ];
    }
}
