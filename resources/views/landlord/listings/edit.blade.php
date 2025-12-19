@extends('layouts.landlord')

@section('page-title', 'Chỉnh sửa bài đăng')


@section('content')
<div class="card">
    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);">
        <h5 class="mb-0"> Chỉnh sửa bài đăng</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('landlord.listings.update', $listing->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Title -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Tiêu đề <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title', $listing->title) }}" 
                           required 
                           class="form-control @error('title') is-invalid @enderror">
                    @error('title')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Category -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Danh mục <span class="text-danger">*</span>
                    </label>
                    <select name="category_id" 
                            required 
                            class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $listing->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                         Số điện thoại <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="phone" 
                           value="{{ old('phone', $listing->phone) }}" 
                           required 
                           class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Mô tả chi tiết <span class="text-danger">*</span>
                    </label>
                    <textarea name="description" 
                              rows="6" 
                              required 
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $listing->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Price, Area & Rooms -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                         Giá (VNĐ/tháng) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           name="price" 
                           value="{{ old('price', $listing->price) }}" 
                           min="0" 
                           required 
                           class="form-control @error('price') is-invalid @enderror">
                    @error('price')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                         Diện tích (m²) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           name="area" 
                           value="{{ old('area', $listing->area) }}" 
                           min="0" 
                           step="0.1" 
                           required 
                           class="form-control @error('area') is-invalid @enderror">
                    @error('area')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        Số lượng phòng <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           name="total_units"
                           value="{{ old('total_units', $listing->total_units) }}"
                           min="1"
                           class="form-control @error('total_units') is-invalid @enderror">
                    @error('total_units')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted d-block">Hệ thống tự trừ phòng khi hợp đồng được ký.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        Phòng còn trống
                    </label>
                    <input type="number" class="form-control" value="{{ $listing->available_units }}" readonly>
                    <small class="text-muted">Không thể chỉnh sửa trực tiếp.</small>
                </div>

                <!-- Map Location -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Vị trí trên bản đồ <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small">Vui lòng click trên bản đồ để ghim vị trí chính xác của phòng trọ</p>
                    <div id="map" style="height: 400px; width: 100%; border: 1px solid #ddd; border-radius: 8px;"></div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $listing->latitude) }}" required>
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $listing->longitude) }}" required>
                    <div class="mt-2">
                        <small class="text-muted">Vị trí đã chọn: <span id="selectedLocation">{{ $listing->address ?? 'Chưa chọn' }}</span></small>
                    </div>
                    @error('latitude')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('longitude')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Địa chỉ chi tiết <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="address" 
                           value="{{ old('address', $listing->address) }}" 
                           required 
                           class="form-control @error('address') is-invalid @enderror">
                    @error('address')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Province, District, Ward -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                         Tỉnh/Thành phố <span class="text-danger">*</span>
                    </label>
                    <select name="province" id="provinceSelect" class="form-select @error('province') is-invalid @enderror" data-selected="{{ old('province', $listing->province) }}">
                        <option value="">Đang tải...</option>
                    </select>
                    @error('province')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                         Quận/Huyện <span class="text-danger">*</span>
                    </label>
                    <select name="district" id="districtSelect" class="form-select @error('district') is-invalid @enderror" data-selected="{{ old('district', $listing->district) }}" disabled>
                        <option value="">Chọn tỉnh trước</option>
                    </select>
                    @error('district')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                         Phường/Xã
                    </label>
                    <select name="ward" id="wardSelect" class="form-select @error('ward') is-invalid @enderror" data-selected="{{ old('ward', $listing->ward) }}" disabled>
                        <option value="">Chọn quận/huyện trước</option>
                    </select>
                    @error('ward')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Current Images -->
                @php
                    $currentImages = json_decode($listing->images ?? '[]', true);
                @endphp
                @if(!empty($currentImages))
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Ảnh hiện tại
                    </label>
                    <div class="row g-2">
                        @foreach($currentImages as $index => $image)
                        <div class="col-md-2">
                            <div class="position-relative">
                                <img src="{{ \App\Helpers\ImageHelper::url($image) }}" alt="Current image" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                                <span class="badge bg-primary position-absolute top-0 end-0">{{ $index + 1 }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- New Images -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Thêm ảnh mới (tối đa 10 ảnh)
                    </label>
                    <input type="file" 
                           name="images[]" 
                           multiple 
                           accept="image/*" 
                           class="form-control @error('images.*') is-invalid @enderror"
                           onchange="previewImages(this)">
                    @error('images.*')
                        <div class="invalid-feedback d-block">
                             {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted">
                         Chọn nhiều ảnh (JPG, PNG, GIF, tối đa 2MB mỗi ảnh)
                    </small>
                    <div id="imagePreview" class="row g-2 mt-2"></div>
                </div>

                <!-- Submit -->
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('landlord.listings.index') }}" class="btn btn-secondary">
                             Hủy
                        </a>
                        <button type="submit" class="btn btn-success">
                             Cập nhật
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewImages(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            if (index < 10) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-2';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="height: 100px; object-fit: cover;">
                            <span class="badge bg-primary position-absolute top-0 end-0">${index + 1}</span>
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

const API_BASE_URL = 'https://provinces.open-api.vn/api/?depth=3';
let provinces = [];

document.addEventListener('DOMContentLoaded', () => {
    initLocationSelectors();
});

async function initLocationSelectors() {
    const provinceSelect = document.getElementById('provinceSelect');
    const districtSelect = document.getElementById('districtSelect');
    const wardSelect = document.getElementById('wardSelect');

    const selectedProvince = provinceSelect.dataset.selected || '';
    const selectedDistrict = districtSelect.dataset.selected || '';
    const selectedWard = wardSelect.dataset.selected || '';

    try {
        await loadAllLocations();
        populateProvinces(provinceSelect, selectedProvince);

        if (selectedProvince) {
            populateDistricts(selectedProvince, districtSelect, selectedDistrict, false);

            if (selectedDistrict) {
                populateWards(selectedProvince, selectedDistrict, wardSelect, selectedWard);
            }
        }
    } catch (error) {
        console.error('Error loading location data', error);
    }

    provinceSelect.addEventListener('change', function () {
        const provinceName = this.value;
        districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        districtSelect.disabled = true;
        wardSelect.disabled = true;

        if (provinceName) {
            populateDistricts(provinceName, districtSelect, '', true);
        }
    });

    districtSelect.addEventListener('change', function () {
        const provinceName = provinceSelect.value;
        const districtName = this.value;
        wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
        wardSelect.disabled = true;

        if (provinceName && districtName) {
            populateWards(provinceName, districtName, wardSelect);
        }
    });
}

async function loadAllLocations() {
    if (provinces.length > 0) {
        return;
    }

    const response = await fetch(API_BASE_URL);
    provinces = await response.json();
}

function populateProvinces(selectEl, selectedValue = '') {
    selectEl.disabled = true;
    selectEl.innerHTML = '<option value="">Chọn tỉnh/thành phố</option>';

    provinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province.name;
        option.textContent = province.name;
        if (selectedValue && normalizeName(selectedValue) === normalizeName(province.name)) {
            option.selected = true;
        }
        selectEl.appendChild(option);
    });

    selectEl.disabled = false;
}

function populateDistricts(provinceName, selectEl, selectedValue = '', shouldTriggerChange = true) {
    const province = provinces.find(p => normalizeName(p.name) === normalizeName(provinceName));
    if (!province) {
        return;
    }

    selectEl.disabled = true;
    selectEl.innerHTML = '<option value="">Chọn quận/huyện</option>';

    (province.districts ?? []).forEach(district => {
        const option = document.createElement('option');
        option.value = district.name;
        option.textContent = district.name;
        if (selectedValue && normalizeName(selectedValue) === normalizeName(district.name)) {
            option.selected = true;
        }
        selectEl.appendChild(option);
    });

    selectEl.disabled = false;
    if (shouldTriggerChange) {
        selectEl.dispatchEvent(new Event('change'));
    }
}

function populateWards(provinceName, districtName, selectEl, selectedValue = '') {
    const province = provinces.find(p => normalizeName(p.name) === normalizeName(provinceName));
    if (!province) {
        return;
    }

    const district = (province.districts ?? []).find(d => normalizeName(d.name) === normalizeName(districtName));
    if (!district) {
        return;
    }

    selectEl.disabled = true;
    selectEl.innerHTML = '<option value="">Chọn phường/xã</option>';

    (district.wards ?? []).forEach(ward => {
        const option = document.createElement('option');
        option.value = ward.name;
        option.textContent = ward.name;
        if (selectedValue && normalizeName(selectedValue) === normalizeName(ward.name)) {
            option.selected = true;
        }
        selectEl.appendChild(option);
    });

    selectEl.disabled = false;
}

function normalizeName(name) {
    return (name || '').toLowerCase().replace('thành phố ', '').replace('tỉnh ', '').trim();
}
</script>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Initialize Map
let map, marker;
const defaultLat = @json(old('latitude', $listing->latitude ?? 10.8231));
const defaultLng = @json(old('longitude', $listing->longitude ?? 106.6297));

document.addEventListener('DOMContentLoaded', function() {
    // Đợi một chút để đảm bảo DOM đã render xong
    setTimeout(function() {
        initMap();
    }, 100);
});

function initMap() {
    // Kiểm tra xem element map có tồn tại không
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Map container not found');
        return;
    }
    
    // Kiểm tra xem Leaflet đã được load chưa
    if (typeof L === 'undefined') {
        console.error('Leaflet library not loaded');
        return;
    }
    
    map = L.map('map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Nếu đã có vị trí, hiển thị marker
    if (document.getElementById('latitude').value && document.getElementById('longitude').value) {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 15);
        updateLocationText(lat, lng);
    }

    // Click on map to set location
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker([lat, lng]).addTo(map);

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        updateLocationText(lat, lng);
    });

    // Try to get user's location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            if (!document.getElementById('latitude').value || !document.getElementById('longitude').value) {
                map.setView([lat, lng], 15);
                marker = L.marker([lat, lng]).addTo(map);
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                updateLocationText(lat, lng);
            }
        });
    }
}

async function updateLocationText(lat, lng) {
    const locationSpan = document.getElementById('selectedLocation');
    if (!locationSpan) return;

    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, {
            headers: {
                'User-Agent': 'HomestayApp/1.0'
            }
        });
        const data = await response.json();
        if (data && data.display_name) {
            locationSpan.textContent = data.display_name;
        } else {
            locationSpan.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        }
    } catch (error) {
        locationSpan.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
}
</script>
@endsection
