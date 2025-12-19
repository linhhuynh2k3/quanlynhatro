@extends('layouts.frontend')

@section('title', 'Tìm kiếm trên bản đồ - Homestay.com')

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Map Container -->
        <div class="col-12">
            <div id="map" style="height: calc(100vh - 200px); min-height: 600px;"></div>
        </div>
    </div>
</div>

<!-- Info Panel (Optional - có thể ẩn/hiện) -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 1000; max-width: 350px;">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="bi bi-map"></i> Tìm kiếm trên bản đồ</h6>
        </div>
        <div class="card-body">
            <p class="small text-muted mb-2">
                <i class="bi bi-info-circle"></i> Nhấn vào các marker trên bản đồ để xem thông tin phòng trọ
            </p>
            <div class="d-flex justify-content-between align-items-center">
                <span class="small">Tổng số: <strong id="totalListings">{{ $listings->count() }}</strong> phòng trọ</span>
                <a href="{{ route('listings.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-list"></i> Xem danh sách
                </a>
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
// Dữ liệu listings từ server - đã được xử lý trong controller
var listingsData = {!! json_encode($listingsData ?? []) !!};

document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo map - Center tại Việt Nam (Hà Nội)
    const map = L.map('map').setView([10.762622, 106.660172], 13); // TP.HCM mặc định

    // Thêm tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Dữ liệu listings từ server
    const listings = listingsData;

    // Icon cho marker
    const defaultIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    // Tạo marker cho mỗi listing
    const markers = [];
    const bounds = [];

    listings.forEach(function(listing) {
        if (listing.latitude && listing.longitude) {
            const lat = parseFloat(listing.latitude);
            const lng = parseFloat(listing.longitude);

            // Tạo popup content
            const popupContent = `
                <div style="min-width: 250px;">
                    <h6 class="mb-2 fw-bold">${escapeHtml(listing.title)}</h6>
                    ${listing.first_image ? `
                        <img src="${listing.first_image}" 
                             alt="${escapeHtml(listing.title)}" 
                             style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;">
                    ` : ''}
                    <div class="mb-2">
                        <strong class="text-danger">${formatPrice(listing.price)}</strong>
                        ${listing.area ? `<span class="text-muted"> / ${listing.area}m²</span>` : ''}
                    </div>
                    ${listing.address ? `
                        <p class="small text-muted mb-2">
                            <i class="bi bi-geo-alt"></i> ${escapeHtml(listing.address)}
                        </p>
                    ` : ''}
                    ${listing.description ? `
                        <p class="small mb-2" style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                            ${escapeHtml(listing.description.substring(0, 100))}...
                        </p>
                    ` : ''}
                    <div class="d-flex gap-2">
                        <a style="color: white;" href="${getListingUrl(listing.id)}" 
                           class="btn btn-primary btn-sm flex-fill" 
                           target="_blank">
                            <i style="color: white;" class="bi bi-eye"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            `;

            // Tạo marker
            const marker = L.marker([lat, lng], { icon: defaultIcon })
                .addTo(map)
                .bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });

            markers.push(marker);
            bounds.push([lat, lng]);
        }
    });

    // Fit map để hiển thị tất cả markers
    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }

    // Helper functions
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }


    function getListingUrl(id) {
        return '{{ url("/listings") }}/' + id;
    }

    // Thêm control để tìm vị trí hiện tại
    if (navigator.geolocation) {
        const locateControl = L.control({ position: 'topleft' });
        locateControl.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'leaflet-control leaflet-bar');
            div.innerHTML = '<a href="#" title="Tìm vị trí của tôi" style="padding: 8px;"><i class="bi bi-geo-alt-fill"></i></a>';
            div.onclick = function(e) {
                e.preventDefault();
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 15);
                    L.marker([lat, lng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                        })
                    })
                    .addTo(map)
                    .bindPopup('<strong>Vị trí của bạn</strong>')
                    .openPopup();
                }, function(error) {
                    alert('Không thể lấy vị trí của bạn. Vui lòng kiểm tra quyền truy cập vị trí.');
                });
            };
            return div;
        };
        locateControl.addTo(map);
    }
});
</script>

<style>
.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.custom-popup .leaflet-popup-content {
    margin: 15px;
}

.leaflet-control {
    background: white;
    border-radius: 4px;
}

.leaflet-control a {
    color: #333;
    text-decoration: none;
}

.leaflet-control a:hover {
    background-color: #f4f4f4;
}
</style>
@endsection

