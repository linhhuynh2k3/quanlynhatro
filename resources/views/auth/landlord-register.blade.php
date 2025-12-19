<x-guest-layout>
    @section('auth-title', 'Đăng ký làm chủ trọ')
    
    <div class="alert alert-info">
        <strong>Lưu ý:</strong> Yêu cầu đăng ký làm chủ trọ sẽ được gửi đến quản trị viên để duyệt. Bạn sẽ nhận được thông báo qua email sau khi được duyệt.
    </div>

    <form method="POST" action="{{ route('landlord.register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-bold">Họ và tên</label>
            <input id="name" 
                   type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   placeholder="Nhập họ và tên">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-bold">Email</label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   placeholder="Nhập email của bạn">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label fw-bold">Số điện thoại</label>
            <input id="phone" 
                   type="tel" 
                   name="phone" 
                   class="form-control @error('phone') is-invalid @enderror" 
                   value="{{ old('phone') }}" 
                   required
                   placeholder="Nhập số điện thoại">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Location on Map -->
        <div class="mb-3">
            <label class="form-label fw-bold">
                Vị trí địa chỉ <span class="text-danger">*</span>
            </label>
            <p class="text-muted small">Vui lòng click trên bản đồ để ghim vị trí địa chỉ của bạn</p>
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

        <!-- Address (Text field for detailed address) -->
        <div class="mb-3">
            <label for="address" class="form-label fw-bold">Địa chỉ chi tiết</label>
            <textarea id="address" 
                      name="address" 
                      class="form-control @error('address') is-invalid @enderror" 
                      rows="2"
                      placeholder="Số nhà, tên đường (nếu có)">{{ old('address') }}</textarea>
            <small class="text-muted">Địa chỉ chi tiết (tùy chọn, có thể để trống nếu đã chọn trên bản đồ)</small>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- CCCD Number -->
        <div class="mb-3">
            <label for="cccd_number" class="form-label fw-bold">Số CCCD/CMND <span class="text-danger">*</span></label>
            <input id="cccd_number" 
                   type="text" 
                   name="cccd_number" 
                   class="form-control @error('cccd_number') is-invalid @enderror" 
                   value="{{ old('cccd_number') }}" 
                   required
                   placeholder="Nhập số CCCD/CMND">
            @error('cccd_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- CCCD Front Image -->
        <div class="mb-3">
            <label for="cccd_front_image" class="form-label fw-bold">Ảnh CCCD mặt trước <span class="text-danger">*</span></label>
            <input id="cccd_front_image" 
                   type="file" 
                   name="cccd_front_image" 
                   class="form-control @error('cccd_front_image') is-invalid @enderror" 
                   accept="image/jpeg,image/png,image/jpg"
                   required>
            <small class="text-muted">Định dạng: JPG, PNG. Tối đa 2MB</small>
            @error('cccd_front_image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div id="cccdFrontPreview" class="mt-2"></div>
        </div>

        <!-- CCCD Back Image -->
        <div class="mb-3">
            <label for="cccd_back_image" class="form-label fw-bold">Ảnh CCCD mặt sau <span class="text-danger">*</span></label>
            <input id="cccd_back_image" 
                   type="file" 
                   name="cccd_back_image" 
                   class="form-control @error('cccd_back_image') is-invalid @enderror" 
                   accept="image/jpeg,image/png,image/jpg"
                   required>
            <small class="text-muted">Định dạng: JPG, PNG. Tối đa 2MB</small>
            @error('cccd_back_image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div id="cccdBackPreview" class="mt-2"></div>
        </div>

        <!-- Business License Image -->
        <div class="mb-3">
            <label for="business_license_image" class="form-label fw-bold">Giấy phép kinh doanh (nếu có)</label>
            <input id="business_license_image" 
                   type="file" 
                   name="business_license_image" 
                   class="form-control @error('business_license_image') is-invalid @enderror" 
                   accept="image/jpeg,image/png,image/jpg">
            <small class="text-muted">Định dạng: JPG, PNG. Tối đa 2MB</small>
            @error('business_license_image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
            <div id="businessLicensePreview" class="mt-2"></div>
        </div>

        <!-- Message/Reason -->
        <div class="mb-3">
            <label for="message" class="form-label fw-bold">Lý do muốn làm chủ trọ</label>
            <textarea id="message" 
                      name="message" 
                      class="form-control @error('message') is-invalid @enderror" 
                      rows="4"
                      placeholder="Vui lòng mô tả lý do bạn muốn đăng ký làm chủ trọ...">{{ old('message') }}</textarea>
            @error('message')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-bold">Mật khẩu</label>
            <div class="input-group">
                <input id="password" 
                       type="password" 
                       name="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       required 
                       autocomplete="new-password"
                       placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', 'togglePasswordIcon1')">
                    <i class="bi bi-eye" id="togglePasswordIcon1"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-bold">Xác nhận mật khẩu</label>
            <div class="input-group">
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       class="form-control" 
                       required 
                       autocomplete="new-password"
                       placeholder="Nhập lại mật khẩu">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation', 'togglePasswordIcon2')">
                    <i class="bi bi-eye" id="togglePasswordIcon2"></i>
                </button>
            </div>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-auth text-white">Gửi yêu cầu đăng ký</button>
        </div>

        <div class="text-center">
            <p class="mb-0 text-muted">Đã có tài khoản?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mt-2">
                Đăng nhập ngay
            </a>
        </div>

        <hr class="my-3">

        <div class="text-center">
            <p class="mb-0 text-muted small">Bạn chỉ muốn tìm phòng trọ?</p>
            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 mt-2">
                Đăng ký người tìm phòng
            </a>
        </div>
    </form>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
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
                    // Auto-fill address field if empty
                    if (!document.getElementById('address').value) {
                        document.getElementById('address').value = address;
                    }
                })
                .catch(() => {
                    document.getElementById('selectedLocation').textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                });
        }

        function togglePassword(fieldId, iconId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        // Preview images
        document.getElementById('cccd_front_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('cccdFrontPreview').innerHTML = 
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">';
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('cccd_back_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('cccdBackPreview').innerHTML = 
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">';
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('business_license_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('businessLicensePreview').innerHTML = 
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</x-guest-layout>

