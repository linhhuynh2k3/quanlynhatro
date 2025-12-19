<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with(['user', 'category'])
            ->where('status', 'approved')
            ->where(function($q) {
                $q->where('expired_at', '>', now())
                  ->orWhereNull('expired_at');
            });

        // Lọc theo danh mục
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Lọc theo tỉnh/thành
        if ($request->filled('province')) {
            $province = trim($request->province);
            // Normalize: loại bỏ "Thành phố", "Tỉnh" ở đầu để match với database
            $provinceNormalized = preg_replace('/^(Thành phố|Tỉnh)\s+/i', '', $province);
            $query->where(function($q) use ($province, $provinceNormalized) {
                // Tìm với tên đầy đủ từ API
                $q->where('province', 'like', '%' . $province . '%')
                  // Tìm với tên đã normalize (không có prefix)
                  ->orWhere('province', 'like', '%' . $provinceNormalized . '%')
                  // Tìm với các biến thể có prefix
                  ->orWhere('province', 'like', '%Thành phố ' . $provinceNormalized . '%')
                  ->orWhere('province', 'like', '%Tỉnh ' . $provinceNormalized . '%');
            });
        }

        // Lọc theo quận/huyện
        if ($request->filled('district')) {
            $district = trim($request->district);
            // Normalize: loại bỏ "Quận", "Huyện", "Thị xã", "Thành phố" ở đầu
            $districtNormalized = preg_replace('/^(Quận|Huyện|Thị xã|Thành phố)\s+/i', '', $district);
            $query->where(function($q) use ($district, $districtNormalized) {
                // Tìm với tên đầy đủ từ API
                $q->where('district', 'like', '%' . $district . '%')
                  // Tìm với tên đã normalize (không có prefix)
                  ->orWhere('district', 'like', '%' . $districtNormalized . '%')
                  // Tìm với các biến thể có prefix
                  ->orWhere('district', 'like', '%Quận ' . $districtNormalized . '%')
                  ->orWhere('district', 'like', '%Huyện ' . $districtNormalized . '%')
                  ->orWhere('district', 'like', '%Thị xã ' . $districtNormalized . '%')
                  ->orWhere('district', 'like', '%Thành phố ' . $districtNormalized . '%');
            });
        }

        // Lọc theo khoảng giá
        if ($request->filled('price_range')) {
            $priceRange = $request->price_range;
            if (strpos($priceRange, '-') !== false) {
                $parts = explode('-', $priceRange);
                $priceMin = $parts[0] ?? null;
                $priceMax = $parts[1] ?? null;
                
                if ($priceMin !== null && $priceMin !== '') {
                    $query->where('price', '>=', $priceMin);
                }
                if ($priceMax !== null && $priceMax !== '') {
                    $query->where('price', '<=', $priceMax);
                }
            }
        } else {
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
            }
        }

        // Lọc theo khoảng diện tích
        if ($request->filled('area_min')) {
            $query->where('area', '>=', $request->area_min);
        }
        if ($request->filled('area_max')) {
            $query->where('area', '<=', $request->area_max);
        }

        // Tìm kiếm theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                  ->orWhere('description', 'like', '%' . $keyword . '%')
                  ->orWhere('address', 'like', '%' . $keyword . '%');
            });
        }

        // Tìm kiếm theo vị trí (latitude, longitude)
        $userLat = $request->get('lat');
        $userLng = $request->get('lng');
        $radius = $request->get('radius', 10); // km, mặc định 10km
        
        if ($userLat && $userLng) {
            // Tính khoảng cách bằng công thức Haversine
            // Chỉ lấy listings có tọa độ
            $query->whereNotNull('latitude')
                  ->whereNotNull('longitude');
            
            // Sử dụng whereRaw để filter distance (thay vì having)
            // Điều này cho phép paginate() hoạt động đúng với PostgreSQL
            $query->whereRaw('(
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) <= ?', [$userLat, $userLng, $userLat, $radius]);
            
            // Thêm select để tính khoảng cách cho việc sắp xếp
            $query->selectRaw('*, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance', [$userLat, $userLng, $userLat]);
        }

        // Sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'distance':
                if ($userLat && $userLng) {
                    $query->orderBy('distance', 'asc');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $listings = $query->paginate(5)->appends($request->query());
        
        // Get selected category if exists
        $selectedCategory = null;
        if ($request->filled('category_id')) {
            $selectedCategory = Category::find($request->category_id);
        }
        
        $categories = Category::where('is_active', true)
            ->withCount(['listings as listings_count' => function ($q) {
                $q->where('status', 'approved')
                    ->where(function ($inner) {
                        $inner->whereNull('expired_at')
                            ->orWhere('expired_at', '>', now());
                    });
            }])
            ->orderBy('position')
            ->get();
        $sliders = Slider::where('is_active', true)->orderBy('position')->get();
        $featured_listings = Listing::where('status', 'approved')
            ->where('is_featured', true)
            ->where('expired_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('frontend.listings.index', compact(
            'listings',
            'categories',
            'sliders',
            'featured_listings',
            'selectedCategory'
        ))->with('title', 'Danh sách bài đăng');
    }

    public function show($id)
    {
        $listing = Listing::with(['user', 'category', 'comments.user', 'comments.replies.user'])
            ->where('status', 'approved')
            ->findOrFail($id);

        // Tăng lượt xem
        $listing->increment('views');

        // Bài đăng liên quan
        $related_listings = Listing::where('category_id', $listing->category_id)
            ->where('id', '!=', $listing->id)
            ->where('status', 'approved')
            ->where('expired_at', '>', now())
            ->limit(4)
            ->get();

        return view('frontend.listings.show', compact('listing', 'related_listings'))->with('title', $listing->title);
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }

    public function map()
    {
        // Lấy tất cả listings đã được duyệt, còn hạn và có tọa độ
        $listings = Listing::with(['user', 'category'])
            ->where('status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function($q) {
                $q->where('expired_at', '>', now())
                  ->orWhereNull('expired_at');
            })
            ->get();

        // Xử lý dữ liệu listings để thêm first_image URL
        $listingsData = $listings->map(function($listing) {
            $images = json_decode($listing->images ?? '[]', true);
            $firstImage = null;
            if (is_array($images) && count($images) > 0) {
                $firstImage = \App\Helpers\ImageHelper::url($images[0]);
            }
            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'price' => $listing->price,
                'area' => $listing->area,
                'address' => $listing->address,
                'description' => $listing->description,
                'latitude' => $listing->latitude,
                'longitude' => $listing->longitude,
                'first_image' => $firstImage
            ];
        })->values()->toArray();

        return view('frontend.listings.map', [
            'listings' => $listings,
            'listingsData' => $listingsData
        ])->with('title', 'Tìm kiếm trên bản đồ');
    }
}
