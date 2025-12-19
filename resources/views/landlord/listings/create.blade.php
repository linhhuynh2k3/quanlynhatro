@extends('layouts.landlord')

@section('page-title')
Đăng bài mới
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);">
        <h5 class="mb-0"> Đăng bài mới</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('landlord.listings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <!-- Title -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Tiêu đề <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required 
                           class="form-control @error('title') is-invalid @enderror"
                           placeholder="VD: Cho thuê phòng trọ đẹp, giá rẻ tại Quận 1">
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
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                           value="{{ old('phone', auth()->user()->phone ?? '') }}" 
                           required 
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="VD: 0909123456">
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
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Mô tả chi tiết về phòng trọ, tiện ích, quy định...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Price, Area & Rooms -->
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                         Giá phòng (VNĐ/tháng) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           name="price" 
                           value="{{ old('price') }}" 
                           min="0" 
                           required 
                           class="form-control @error('price') is-invalid @enderror"
                           placeholder="VD: 3000000">
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
                           value="{{ old('area') }}" 
                           min="0" 
                           step="0.1" 
                           required 
                           class="form-control @error('area') is-invalid @enderror"
                           placeholder="VD: 25">
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
                           value="{{ old('total_units', 1) }}"
                           min="1"
                           class="form-control @error('total_units') is-invalid @enderror"
                           placeholder="VD: 5">
                    @error('total_units')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted">Hệ thống tự tính số phòng còn trống.</small>
                </div>

                <!-- Utility Prices -->
                <div class="col-12 mt-3">
                    <h5 class="fw-bold mb-3">Giá dịch vụ (tùy chọn)</h5>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        Giá điện (VNĐ/kWh)
                    </label>
                    <input type="number" 
                           name="electricity_price" 
                           value="{{ old('electricity_price') }}" 
                           min="0"
                           step="0.01"
                           class="form-control @error('electricity_price') is-invalid @enderror"
                           placeholder="VD: 3500">
                    @error('electricity_price')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted">Giá điện trên mỗi kWh</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        Giá nước (VNĐ/m³)
                    </label>
                    <input type="number" 
                           name="water_price" 
                           value="{{ old('water_price') }}" 
                           min="0"
                           step="0.01"
                           class="form-control @error('water_price') is-invalid @enderror"
                           placeholder="VD: 15000">
                    @error('water_price')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted">Giá nước trên mỗi m³</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        Giá Wifi (VNĐ/tháng)
                    </label>
                    <input type="number" 
                           name="wifi_price" 
                           value="{{ old('wifi_price') }}" 
                           min="0"
                           step="0.01"
                           class="form-control @error('wifi_price') is-invalid @enderror"
                           placeholder="VD: 100000">
                    @error('wifi_price')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted">Giá wifi hàng tháng</small>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">
                        Giá rác (VNĐ/tháng)
                    </label>
                    <input type="number" 
                           name="garbage_price" 
                           value="{{ old('garbage_price') }}" 
                           min="0"
                           step="0.01"
                           class="form-control @error('garbage_price') is-invalid @enderror"
                           placeholder="VD: 50000">
                    @error('garbage_price')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                    <small class="text-muted">Giá rác hàng tháng</small>
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Địa chỉ chi tiết <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           name="address" 
                           value="{{ old('address') }}" 
                           required 
                           class="form-control @error('address') is-invalid @enderror"
                           placeholder="VD: Số 123, Đường ABC">
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
                    <select name="province" id="provinceSelect" class="form-select @error('province') is-invalid @enderror" data-selected="{{ old('province') }}">
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
                    <select name="district" id="districtSelect" class="form-select @error('district') is-invalid @enderror" data-selected="{{ old('district') }}" disabled>
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
                    <select name="ward" id="wardSelect" class="form-select @error('ward') is-invalid @enderror" data-selected="{{ old('ward') }}" disabled>
                        <option value="">Chọn quận/huyện trước</option>
                    </select>
                    @error('ward')
                        <div class="invalid-feedback">
                             {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Map Location -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                        Vị trí trên bản đồ <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small">Vui lòng click trên bản đồ để ghim vị trí chính xác của phòng trọ</p>
                    <div id="map" style="height: 400px; width: 100%; border: 1px solid #ddd; border-radius: 8px;"></div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>
                    <div class="mt-2">
                        <small class="text-muted">
                            Vị trí đã chọn: <span id="selectedLocation">Chưa chọn</span>
                        </small>
                    </div>
                    @error('latitude')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('longitude')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Images -->
                <div class="col-12">
                    <label class="form-label fw-bold">
                         Hình ảnh (tối đa 10 ảnh)
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

                <!-- Payment Options -->
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Tùy chọn thanh toán đăng tin</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Payment Type -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        Loại thanh toán <span class="text-danger">*</span>
                                    </label>
                                    <select name="payment_type" 
                                            id="payment_type"
                                            required 
                                            class="form-select @error('payment_type') is-invalid @enderror"
                                            onchange="updatePrice()">
                                        <option value="daily" {{ old('payment_type', 'daily') == 'daily' ? 'selected' : '' }}>
                                            Theo ngày (2.000₫/ngày)
                                        </option>
                                        <option value="weekly" {{ old('payment_type') == 'weekly' ? 'selected' : '' }}>
                                            Theo tuần (12.000₫/tuần - 7 ngày)
                                        </option>
                                        <option value="monthly" {{ old('payment_type') == 'monthly' ? 'selected' : '' }}>
                                            Theo tháng (48.000₫/tháng - 30 ngày)
                                        </option>
                                    </select>
                                    @error('payment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Duration Days -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">
                                        Số ngày đăng <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           name="duration_days" 
                                           id="duration_days"
                                           value="{{ old('duration_days', 30) }}" 
                                           min="5" 
                                           max="365"
                                           required 
                                           class="form-control @error('duration_days') is-invalid @enderror"
                                           onchange="updatePrice()">
                                    @error('duration_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">
                                        Tối thiểu 5 ngày cho tin thường
                                    </small>
                                </div>

                                <!-- Price Preview -->
                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        <strong>Giá dự kiến:</strong> 
                                        <span id="pricePreview" class="fw-bold text-primary">Đang tính...</span>
                                        <br>
                                        <small class="text-muted">
                                            Tiền sẽ được trừ khi bài đăng được duyệt. Nếu không đủ số dư, bài đăng sẽ không được duyệt.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('landlord.listings.index') }}" class="btn btn-secondary">
                             Hủy
                        </a>
                        <button type="submit" class="btn btn-success">
                             Đăng bài
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
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

// Initialize Map
let map, marker;
const defaultLat = {{ old('latitude', '10.8231') }};
const defaultLng = {{ old('longitude', '106.6297') }};

document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

function initMap() {
    map = L.map('map').setView([defaultLat, defaultLng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add marker if location is already set
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
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
        
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        updateLocationText(lat, lng);
    });
    
    // Try to get user's current location
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

function updateLocationText(lat, lng) {
    // Reverse geocoding
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

// Calculate listing price
function updatePrice() {
    const paymentType = document.getElementById('payment_type').value;
    const durationDays = parseInt(document.getElementById('duration_days').value) || 5;
    const pricePreview = document.getElementById('pricePreview');
    
    // Giá tin thường
    const dailyPrice = 2000; // 2.000₫/ngày
    const weeklyPrice = 12000; // 12.000₫/tuần (7 ngày)
    const monthlyPrice = 48000; // 48.000₫/tháng (30 ngày)
    
    let totalPrice = 0;
    let description = '';
    
    // Đảm bảo tối thiểu 5 ngày
    const minDays = 5;
    const actualDays = Math.max(durationDays, minDays);
    
    switch(paymentType) {
        case 'daily':
            totalPrice = dailyPrice * actualDays;
            description = `${dailyPrice.toLocaleString('vi-VN')}₫/ngày × ${actualDays} ngày`;
            break;
        case 'weekly':
            const weeks = Math.ceil(actualDays / 7);
            totalPrice = weeklyPrice * weeks;
            description = `${weeklyPrice.toLocaleString('vi-VN')}₫/tuần × ${weeks} tuần (${actualDays} ngày)`;
            break;
        case 'monthly':
            const months = Math.ceil(actualDays / 30);
            totalPrice = monthlyPrice * months;
            description = `${monthlyPrice.toLocaleString('vi-VN')}₫/tháng × ${months} tháng (${actualDays} ngày)`;
            break;
    }
    
    pricePreview.textContent = `${totalPrice.toLocaleString('vi-VN')}₫ (${description})`;
}

// Initialize price on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePrice();
});
</script>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection
