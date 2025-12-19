@extends('layouts.frontend')

@section('title', 'Danh sách bài đăng - Homestay.com')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="hero-title fade-in-up">Tìm Phòng Trọ, Nhà Trọ Phù Hợp</h1>
                <p class="hero-subtitle fade-in-up">Hơn 10,000+ tin đăng mới mỗi ngày. Tìm kiếm nhanh chóng, dễ dàng!</p>
            </div>
        </div>
    </div>
</div>

<!-- Search Box -->
<div class="container">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('listings.map') }}" class="btn btn-primary">
            <i class="bi bi-map"></i> Tìm kiếm trên bản đồ
        </a>
    </div>
    <div class="search-box">
        <form action="{{ route('listings.index') }}" method="GET" id="searchForm">
            <!-- Hidden field to preserve category_id -->
            @if(request('category_id'))
            <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Từ khóa</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Nhập từ khóa tìm kiếm...">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tỉnh/Thành phố</label>
                    <select name="province" id="provinceSelect" class="form-select">
                        <option value="">Chọn tỉnh/thành phố</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Quận/Huyện</label>
                    <select name="district" id="districtSelect" class="form-select" disabled>
                        <option value="">Chọn quận/huyện</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Giá (VNĐ/tháng)</label>
                    <select name="price_range" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="0-2000000" {{ request('price_range') == '0-2000000' ? 'selected' : '' }}>Dưới 2 triệu</option>
                        <option value="2000000-5000000" {{ request('price_range') == '2000000-5000000' ? 'selected' : '' }}>2 - 5 triệu</option>
                        <option value="5000000-10000000" {{ request('price_range') == '5000000-10000000' ? 'selected' : '' }}>5 - 10 triệu</option>
                        <option value="10000000-20000000" {{ request('price_range') == '10000000-20000000' ? 'selected' : '' }}>10 - 20 triệu</option>
                        <option value="20000000-" {{ request('price_range') == '20000000-' ? 'selected' : '' }}>Trên 20 triệu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Diện tích (m²)</label>
                    <input type="number" name="area_min" value="{{ request('area_min') }}" class="form-control" placeholder="Từ">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <input type="number" name="area_max" value="{{ request('area_max') }}" class="form-control" placeholder="Đến">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tìm theo vị trí</label>
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#locationModal">
                        <i class="bi bi-geo-alt"></i> Chọn vị trí trên bản đồ
                    </button>
                    <input type="hidden" name="lat" id="selectedLat" value="{{ request('lat') }}">
                    <input type="hidden" name="lng" id="selectedLng" value="{{ request('lng') }}">
                    <input type="hidden" name="radius" id="selectedRadius" value="{{ request('radius', 10) }}">
                    @if(request('lat') && request('lng'))
                    <small class="text-success d-block mt-1">
                        <i class="bi bi-check-circle"></i> Đã chọn vị trí ({{ request('radius', 10) }}km)
                        <button type="button" class="btn btn-sm btn-link p-0" onclick="clearLocation()">Xóa</button>
                    </small>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <button type="submit" class="btn btn-danger w-100">Tìm kiếm</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn vị trí tìm kiếm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Bán kính tìm kiếm (km)</label>
                    <input type="range" class="form-range" id="radiusSlider" min="1" max="50" value="{{ request('radius', 10) }}" oninput="updateRadius(this.value)">
                    <div class="d-flex justify-content-between">
                        <span>1 km</span>
                        <span id="radiusValue">{{ request('radius', 10) }} km</span>
                        <span>50 km</span>
                    </div>
                </div>
                <div id="map" style="height: 400px; width: 100%;"></div>
                <div class="mt-3">
                    <p class="mb-1"><strong>Vị trí đã chọn:</strong></p>
                    <p class="mb-0" id="selectedLocation">Chưa chọn vị trí</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="confirmLocation()">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container my-5">
    <!-- Category Filter Badge -->
    @if(isset($selectedCategory))
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <strong>Đang xem danh mục:</strong> {{ $selectedCategory->name }}
            <a href="{{ route('listings.index') }}" class="btn btn-sm btn-outline-danger ms-3">
                <i class="bi bi-x-circle"></i> Xóa bộ lọc
            </a>
        </div>
    </div>
    @endif
    
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Slider -->
            @if(isset($sliders) && $sliders->count() > 0)
            <div class="mb-4">
                <div id="sliderCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-3 overflow-hidden">
                        @foreach($sliders as $index => $slider)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ \App\Helpers\ImageHelper::url($slider->image) }}" class="d-block w-100" style="height: 300px; object-fit: cover;" alt="{{ $slider->title }}">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                                <h5>{{ $slider->title }}</h5>
                                @if($slider->link)
                                <a href="{{ $slider->link }}" class="btn btn-danger btn-sm mt-2">Xem thêm</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($sliders->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#sliderCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#sliderCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                    @endif
                </div>
            </div>
            @endif

            <!-- Results Count -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    Kết quả tìm kiếm: <span class="text-danger">{{ $listings->total() }}</span> tin đăng
                </h4>
            </div>

            <!-- Listings Grid -->
            <div id="listingsContainer">
                @forelse($listings as $listing)
                <div class="listing-card fade-in-up">
                    <div class="row g-0">
                        <!-- Image -->
                        <div class="col-md-4">
                            <div class="listing-image">
                                @php
                                    $images = json_decode($listing->images ?? '[]', true);
                                    $firstImage = !empty($images) ? $images[0] : 'default-listing.jpg';
                                @endphp
                                <img src="{{ \App\Helpers\ImageHelper::url($firstImage) }}" alt="{{ $listing->title }}">
                                @if($listing->is_featured)
                                <span class="badge-featured">Nổi bật</span>
                                @endif
                                @if($listing->created_at->diffInDays(now()) <= 3)
                                <span class="badge-new">Mới</span>
                                @endif
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="col-md-8">
                            <div class="listing-content">
                                <h3 class="listing-title">
                                    <a href="{{ route('listings.show', $listing->id) }}">{{ $listing->title }}</a>
                                </h3>
                                <p class="listing-description">{{ Str::limit($listing->description, 120) }}</p>
                                <div class="listing-info">
                                    <span class="info-badge price">
                                        {{ number_format($listing->price) }} VNĐ/tháng
                                    </span>
                                    <span class="info-badge area">
                                        {{ $listing->area }} m²
                                    </span>
                                    <span class="info-badge availability {{ $listing->available_units > 0 ? 'text-success' : 'text-danger' }}">
                                        Còn {{ $listing->available_units }}/{{ $listing->total_units }} phòng
                                    </span>
                                    <span class="info-badge location">
                                        {{ $listing->district }}, {{ $listing->province }}
                                    </span>
                                </div>
                                <div class="listing-footer">
                                    <span class="listing-time">
                                        {{ \App\Helpers\DateTimeHelper::diffForHumans($listing->created_at) }}
                                    </span>
                                    <a href="{{ route('listings.show', $listing->id) }}" class="btn-view-detail">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <h4 class="mt-3 text-muted">Không tìm thấy bài đăng nào</h4>
                    <p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc điều chỉnh bộ lọc</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($listings->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $listings->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Featured Listings -->
            @if(isset($featured_listings) && $featured_listings->count() > 0)
            <div class="sidebar-card">
                <h5 class="sidebar-title">Tin nổi bật</h5>
                <div class="featured-listings">
                    @foreach($featured_listings->take(5) as $featured)
                    <div class="featured-item">
                        @php
                            $images = json_decode($featured->images ?? '[]', true);
                            $firstImage = !empty($images) ? $images[0] : 'default-listing.jpg';
                        @endphp
                        <img src="{{ \App\Helpers\ImageHelper::url($firstImage) }}" alt="{{ $featured->title }}">
                        <div class="featured-item-content">
                            <h5>
                                <a href="{{ route('listings.show', $featured->id) }}">
                                    {{ Str::limit($featured->title, 50) }}
                                </a>
                            </h5>
                            <p class="price mb-0">{{ number_format($featured->price) }} VNĐ</p>
                            <small class="text-muted">Còn {{ $featured->available_units }}/{{ $featured->total_units }} phòng</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Categories -->
            @if(isset($categories) && $categories->count() > 0)
            <div class="sidebar-card">
                <h5 class="sidebar-title">Danh mục</h5>
                <ul class="category-list">
                    @foreach($categories as $category)
                    <li>
                        <a href="{{ route('listings.index', ['category_id' => $category->id]) }}">
                            <span>{{ $category->name }}</span>
                            <span class="category-count">{{ $category->listings_count ?? 0 }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Quick Links -->
            <div class="sidebar-card">
                <h5 class="sidebar-title">Liên kết nhanh</h5>
                <ul class="category-list">
                    <li><a href="#"><span>Hướng dẫn đăng tin</span></a></li>
                    <li><a href="#"><span>Quy định đăng tin</span></a></li>
                    <li><a href="#"><span>Bảng giá dịch vụ</span></a></li>
                    <li><a href="#"><span>Liên hệ hỗ trợ</span></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map, marker, circle;
let selectedLat = {{ request('lat', 'null') }};
let selectedLng = {{ request('lng', 'null') }};
let selectedRadius = {{ request('radius', 10) }};

function initMap() {
    // Default to Ho Chi Minh City if no location selected
    const defaultLat = selectedLat || 10.8231;
    const defaultLng = selectedLng || 106.6297;
    
    map = L.map('map').setView([defaultLat, defaultLng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add marker if location is selected
    if (selectedLat && selectedLng) {
        marker = L.marker([selectedLat, selectedLng]).addTo(map);
        updateCircle(selectedLat, selectedLng, selectedRadius);
    }
    
    // Click on map to select location
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
        
        updateCircle(lat, lng, selectedRadius);
        updateLocationText(lat, lng);
        
        selectedLat = lat;
        selectedLng = lng;
    });
    
    // Try to get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            if (!selectedLat || !selectedLng) {
                map.setView([lat, lng], 13);
                selectedLat = lat;
                selectedLng = lng;
                marker = L.marker([lat, lng]).addTo(map);
                updateCircle(lat, lng, selectedRadius);
                updateLocationText(lat, lng);
            }
        });
    }
}

function updateCircle(lat, lng, radius) {
    if (circle) {
        map.removeLayer(circle);
    }
    // Convert km to meters
    circle = L.circle([lat, lng], {
        radius: radius * 1000,
        color: '#3388ff',
        fillColor: '#3388ff',
        fillOpacity: 0.2
    }).addTo(map);
}

function updateLocationText(lat, lng) {
    // Reverse geocoding using Nominatim (free)
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            const address = data.display_name || `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            document.getElementById('selectedLocation').textContent = address;
        })
        .catch(() => {
            document.getElementById('selectedLocation').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        });
}

function updateRadius(value) {
    selectedRadius = value;
    document.getElementById('radiusValue').textContent = value + ' km';
    if (selectedLat && selectedLng) {
        updateCircle(selectedLat, selectedLng, selectedRadius);
    }
}

function confirmLocation() {
    if (selectedLat && selectedLng) {
        document.getElementById('selectedLat').value = selectedLat;
        document.getElementById('selectedLng').value = selectedLng;
        document.getElementById('selectedRadius').value = selectedRadius;
        document.getElementById('locationModal').querySelector('.btn-close').click();
        // Optionally submit form
        // document.getElementById('searchForm').submit();
    } else {
        alert('Vui lòng chọn vị trí trên bản đồ');
    }
}

function clearLocation() {
    document.getElementById('selectedLat').value = '';
    document.getElementById('selectedLng').value = '';
    document.getElementById('selectedRadius').value = '';
    location.reload();
}

// Initialize map when modal is shown
document.getElementById('locationModal').addEventListener('shown.bs.modal', function() {
    if (!map) {
        initMap();
    } else {
        map.invalidateSize();
    }
});
</script>

<script>
// Load danh sách tỉnh thành từ API
const API_BASE_URL = 'https://provinces.open-api.vn/api/v1/';

let provinces = [];
let districts = [];

// Load tỉnh thành khi trang load
document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
});

// Normalize province name: remove "Thành phố", "Tỉnh" prefix
function normalizeProvinceName(name) {
    return name.replace(/^(Thành phố|Tỉnh)\s+/i, '');
}

// Load danh sách tỉnh thành
async function loadProvinces() {
    const provinceSelect = document.getElementById('provinceSelect');
    const selectedProvince = '{{ request('province') }}';
    
    try {
        // Show loading
        provinceSelect.disabled = true;
        provinceSelect.innerHTML = '<option value="">Đang tải...</option>';
        
        const response = await fetch(API_BASE_URL + '?depth=1');
        if (!response.ok) {
            throw new Error('Failed to load provinces');
        }
        
        const data = await response.json();
        provinces = data;
        
        // Clear and add provinces
        provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';
        
        data.forEach(province => {
            const option = document.createElement('option');
            option.value = province.name; // Giữ nguyên tên từ API để hiển thị
            option.textContent = province.name;
            // So sánh cả tên đầy đủ và tên đã normalize
            if (selectedProvince && (province.name === selectedProvince || normalizeProvinceName(province.name) === normalizeProvinceName(selectedProvince))) {
                option.selected = true;
            }
            provinceSelect.appendChild(option);
        });
        
        provinceSelect.disabled = false;
        
        // If province was selected, load districts
        if (selectedProvince) {
            // Tìm province match (có thể là tên đầy đủ hoặc đã normalize)
            let province = provinces.find(p => p.name === selectedProvince);
            if (!province) {
                province = provinces.find(p => normalizeProvinceName(p.name) === normalizeProvinceName(selectedProvince));
            }
            if (province) {
                await loadDistricts(province.name, '{{ request('district') }}');
            }
        }
    } catch (error) {
        console.error('Error loading provinces:', error);
        provinceSelect.innerHTML = '<option value="">Lỗi khi tải danh sách</option>';
        provinceSelect.disabled = false;
    }
}

// Load danh sách quận huyện khi chọn tỉnh thành
document.getElementById('provinceSelect').addEventListener('change', async function() {
    const provinceName = this.value;
    const districtSelect = document.getElementById('districtSelect');
    
    if (provinceName) {
        await loadDistricts(provinceName);
    } else {
        // Reset district select
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        districtSelect.disabled = true;
    }
});

// Normalize district name: remove "Quận", "Huyện", "Thị xã" prefix
function normalizeDistrictName(name) {
    return name.replace(/^(Quận|Huyện|Thị xã|Thành phố)\s+/i, '');
}

// Load danh sách quận huyện
async function loadDistricts(provinceName, selectedDistrict = null) {
    const districtSelect = document.getElementById('districtSelect');
    
    try {
        // Find province code
        let province = provinces.find(p => p.name === provinceName);
        
        if (!province) {
            // If not found in cache, reload provinces
            await loadProvinces();
            province = provinces.find(p => p.name === provinceName);
            if (!province) {
                throw new Error('Province not found');
            }
        }
        
        // Show loading
        districtSelect.disabled = true;
        districtSelect.innerHTML = '<option value="">Đang tải...</option>';
        
        // Load districts with depth=2
        const response = await fetch(API_BASE_URL + 'p/' + province.code + '?depth=2');
        if (!response.ok) {
            throw new Error('Failed to load districts');
        }
        
        const data = await response.json();
        districts = data.districts || [];
        
        // Clear and add districts
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        
        districts.forEach(district => {
            const option = document.createElement('option');
            option.value = district.name; // Giữ nguyên tên từ API
            option.textContent = district.name;
            // So sánh cả tên đầy đủ và tên đã normalize
            if (selectedDistrict) {
                if (district.name === selectedDistrict || 
                    normalizeDistrictName(district.name) === normalizeDistrictName(selectedDistrict) ||
                    district.name.includes(selectedDistrict) ||
                    selectedDistrict.includes(normalizeDistrictName(district.name))) {
                    option.selected = true;
                }
            }
            districtSelect.appendChild(option);
        });
        
        districtSelect.disabled = false;
    } catch (error) {
        console.error('Error loading districts:', error);
        districtSelect.innerHTML = '<option value="">Lỗi khi tải danh sách</option>';
        districtSelect.disabled = false;
    }
}
</script>
@endsection
