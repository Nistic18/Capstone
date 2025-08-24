@php
    $role = auth()->check() ? auth()->user()->role : null;
@endphp

@extends('layouts.app')
@section('title', 'Shop Location')
@section('content')
<div class="mt-5">
    {{-- Header Section --}}
    <div class="card border-0 shadow-lg mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-map-marked-alt text-white" style="font-size: 3rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">
                {{ $role === 'buyer' ? '🗺️ Shop Locations' : '📍 Set Your Location' }}
            </h1>
            <p class="lead text-white-50 mb-4">
                {{ $role === 'buyer' ? 'Discover fish suppliers and stores in your area' : 'Mark your store location for customers to find you' }}
            </p>
            
            {{-- Quick Stats --}}
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <i class="fas fa-store text-white me-2"></i>
                    <span class="text-white">{{ $locations->count() }} Locations</span>
                </div>
                <div class="d-flex align-items-center px-3 py-2 rounded-pill" 
                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <i class="fas fa-users text-white me-2"></i>
                    <span class="text-white">{{ $users->count() }} Active Users</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Map Controls Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #2c3e50;">
                <i class="fas fa-compass me-2" style="color: #667eea;"></i>
                Interactive Map
            </h2>
            <p class="text-muted mb-0">
                {{ $role === 'buyer' ? 'Find stores near you' : 'Click on the map to set your location' }}
            </p>
        </div>
        
        <div class="d-flex gap-2">
            <button class="btn btn-outline-info" onclick="getCurrentLocation()" 
                    style="border-radius: 15px;">
                <i class="fas fa-location-arrow me-1"></i>My Location
            </button>
            <button class="btn btn-outline-secondary" onclick="resetMapView()" 
                    style="border-radius: 15px;">
                <i class="fas fa-search-location me-1"></i>Reset View
            </button>
        </div>
    </div>

    {{-- Map Container --}}
    <div class="row">
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header border-0 py-3" style="background: linear-gradient(45deg, #f8f9fa, #e9ecef);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold" style="color: #2c3e50;">
                            <i class="fas fa-map me-2" style="color: #667eea;"></i>
                            Fish Market Locations
                        </h5>
                        
                        {{-- Map Controls --}}
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                        style="border-radius: 10px;" 
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <ul class="dropdown-menu" style="border-radius: 10px;">
                                    <li><a class="dropdown-item" href="#" onclick="filterMarkers('all')">
                                        <i class="fas fa-globe me-2"></i>All Locations
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterMarkers('supplier')">
                                        <i class="fas fa-circle text-success me-2"></i>Suppliers
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterMarkers('reseller')">
                                        <i class="fas fa-circle text-danger me-2"></i>Resellers
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterMarkers('buyer')">
                                        <i class="fas fa-circle text-info me-2"></i>Buyers
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; position: relative;">
                        {{-- Loading Overlay --}}
                        <div id="map-loading" class="position-absolute top-50 start-50 translate-middle" style="z-index: 1000;">
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-2" role="status"></div>
                                <p class="text-muted">Loading map...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Instructions Card --}}
            @if($role !== 'buyer')
            <div class="card border-0 shadow-sm mt-4" style="border-radius: 15px; background: rgba(40, 167, 69, 0.05); border-left: 4px solid #28a745;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle text-success me-3" style="font-size: 1.5rem;"></i>
                        <div>
                            <h6 class="mb-1 fw-bold text-success">How to Set Your Location</h6>
                            <p class="mb-0 text-muted small">
                                Click anywhere on the map to mark your store location. Enter your store name when prompted.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-3 col-md-4">
            {{-- Legend Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header border-0 py-3" style="background: rgba(102, 126, 234, 0.05);">
                    <h6 class="mb-0 fw-bold" style="color: #2c3e50;">
                        <i class="fas fa-list me-2" style="color: #667eea;"></i>
                        Map Legend
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-marker bg-success rounded-circle me-3" style="width: 16px; height: 16px;"></div>
                        <span class="small fw-semibold">Suppliers</span>
                    </div>
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-marker bg-danger rounded-circle me-3" style="width: 16px; height: 16px;"></div>
                        <span class="small fw-semibold">Resellers</span>
                    </div>
                    <div class="legend-item d-flex align-items-center mb-2">
                        <div class="legend-marker bg-info rounded-circle me-3" style="width: 16px; height: 16px;"></div>
                        <span class="small fw-semibold">Buyers</span>
                    </div>
                    <div class="legend-item d-flex align-items-center">
                        <div class="legend-marker bg-warning rounded-circle me-3" style="width: 16px; height: 16px;"></div>
                        <span class="small fw-semibold">Your Location</span>
                    </div>
                </div>
            </div>

            {{-- Location Stats --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header border-0 py-3" style="background: rgba(102, 126, 234, 0.05);">
                    <h6 class="mb-0 fw-bold" style="color: #2c3e50;">
                        <i class="fas fa-chart-pie me-2" style="color: #667eea;"></i>
                        Location Statistics
                    </h6>
                </div>
                <div class="card-body py-3">
                    @php
                        $supplierCount = $locations->where('user.role', 'supplier')->count();
                        $resellerCount = $locations->where('user.role', 'reseller')->count();
                        $buyerCount = $locations->where('user.role', 'buyer')->count();
                    @endphp
                    
                    <div class="stat-item d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-store text-success me-2"></i>
                            <span class="small">Suppliers</span>
                        </div>
                        <span class="badge bg-success">{{ $supplierCount }}</span>
                    </div>
                    
                    <div class="stat-item d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-shopping-cart text-danger me-2"></i>
                            <span class="small">Resellers</span>
                        </div>
                        <span class="badge bg-danger">{{ $resellerCount }}</span>
                    </div>
                    
                    <div class="stat-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user text-info me-2"></i>
                            <span class="small">Buyers</span>
                        </div>
                        <span class="badge bg-info">{{ $buyerCount }}</span>
                    </div>
                </div>
            </div>

            {{-- Nearby Locations --}}
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header border-0 py-3" style="background: rgba(102, 126, 234, 0.05);">
                    <h6 class="mb-0 fw-bold" style="color: #2c3e50;">
                        <i class="fas fa-map-pin me-2" style="color: #667eea;"></i>
                        Recent Locations
                    </h6>
                </div>
                <div class="card-body py-2" style="max-height: 200px; overflow-y: auto;">
                    @forelse($locations->take(5) as $location)
                        <div class="location-item d-flex align-items-center py-2 border-bottom">
                            <div class="me-3">
                                @php
                                    $roleColor = match($location->user->role ?? 'buyer') {
                                        'supplier' => 'success',
                                        'reseller' => 'danger',
                                        default => 'info'
                                    };
                                @endphp
                                <div class="bg-{{ $roleColor }} rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 30px; height: 30px;">
                                    <i class="fas fa-store text-white" style="font-size: 0.7rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small fw-semibold">{{ $location->location_name }}</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.75rem;">
                                    {{ $location->user->name ?? 'Unknown' }} • {{ ucfirst($location->user->role ?? 'buyer') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fas fa-map text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="text-muted small mb-0">No locations found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Form --}}
    @if($role !== 'buyer')
    <form id="location-form" method="POST" action="{{ route('locations.store') }}" style="display: none;">
        @csrf
        <input type="hidden" id="lat" name="latitude">
        <input type="hidden" id="lng" name="longitude">
        <input type="hidden" id="name" name="location_name">
        <input type="hidden" name="type" value="store">
    </form>
    @endif
</div>

{{-- Location Name Modal --}}
<div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title fw-bold" style="color: #2c3e50;">
                    <i class="fas fa-map-marker-alt text-primary me-2"></i>
                    Set Store Location
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #2c3e50;">Store/Business Name</label>
                    <input type="text" id="locationNameInput" class="form-control form-control-lg" 
                           placeholder="e.g., Fresh Fish Market" 
                           style="border-radius: 15px; border: 2px solid #e9ecef;">
                    <div class="form-text">
                        <i class="fas fa-info-circle text-info me-1"></i>
                        This name will be visible to customers on the map
                    </div>
                </div>
                <div class="alert alert-info border-0" style="background: rgba(13, 202, 240, 0.1); border-radius: 10px; color:black;">
                    <i class="fas fa-lightbulb text-info me-2"></i>
                    <strong>Tip:</strong> Use a descriptive name that helps customers identify your business easily.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" id="confirmLocation" class="btn btn-success" style="border-radius: 10px;">
                    <i class="fas fa-map-pin me-1"></i>Set Location
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet Core CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Geocoder Plugin -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

{{-- Custom CSS --}}
<style>
    .leaflet-popup-content {
        border-radius: 10px;
        padding: 10px;
    }
    
    .leaflet-popup-content-wrapper {
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .leaflet-control-container .leaflet-control {
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    
    .btn-outline-info {
        border-color: #0dcaf0;
        color: #0dcaf0;
        transition: all 0.3s ease;
    }
    
    .btn-outline-info:hover {
        background-color: #0dcaf0;
        border-color: #0dcaf0;
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
        transform: translateY(-1px);
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .location-item:hover {
        background: rgba(102, 126, 234, 0.05);
        border-radius: 8px;
    }
    
    .legend-marker {
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2rem;
        }
        
        #map {
            height: 300px !important;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
        }
    }
</style>

<script>
    let map, tempMarker;
    let currentLat, currentLng;
    let allMarkers = [];
    
    // Initialize map
    function initializeMap() {
        map = L.map('map').setView([14.5995, 120.9842], 13); // Manila
        
        // OSM tiles with custom styling
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { 
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Hide loading overlay
        document.getElementById('map-loading').style.display = 'none';
        
        // Add geocoder
        const geocoder = L.Control.Geocoder.nominatim({ 
            geocodingQueryParams: { countrycodes: 'ph' } 
        });
        L.Control.geocoder({ 
            defaultMarkGeocode: false, 
            geocoder,
            placeholder: 'Search locations...',
            collapsed: false
        })
        .on('markgeocode', function(e) { 
            map.setView(e.geocode.center, 16); 
        })
        .addTo(map);
    }

    // Role-based icons with modern colors
    const roleIcons = {
        buyer: L.divIcon({
            html: '<div class="custom-marker buyer-marker"><i class="fas fa-user"></i></div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        }),
        reseller: L.divIcon({
            html: '<div class="custom-marker reseller-marker"><i class="fas fa-shopping-cart"></i></div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        }),
        supplier: L.divIcon({
            html: '<div class="custom-marker supplier-marker"><i class="fas fa-store"></i></div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        }),
        user: L.divIcon({
            html: '<div class="custom-marker user-marker"><i class="fas fa-map-pin"></i></div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        })
    };

    // Helpers
    function isValidLatLng(lat, lng) {
        return typeof lat === 'number' && typeof lng === 'number' &&
               isFinite(lat) && isFinite(lng) &&
               Math.abs(lat) <= 90 && Math.abs(lng) <= 180;
    }

    function getIconForRole(role) {
        const key = (role || 'buyer').toString().trim().toLowerCase();
        return roleIcons[key] || roleIcons['buyer'];
    }

    function safeAddMarker(lat, lng, role, popupHTML, type = 'location') {
        try {
            if (!isValidLatLng(lat, lng)) {
                console.warn('Skipped invalid lat/lng:', lat, lng, role);
                return null;
            }
            const marker = L.marker([lat, lng], { 
                icon: getIconForRole(role) 
            }).addTo(map);
            
            if (popupHTML) {
                marker.bindPopup(popupHTML, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });
            }
            
            // Store marker with metadata for filtering
            marker.markerRole = role;
            marker.markerType = type;
            allMarkers.push(marker);
            
            return marker;
        } catch (e) {
            console.error('Failed to add marker:', e);
            return null;
        }
    }

    // Get current location
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 16);
                    
                    // Add current location marker
                    const currentLocationMarker = L.marker([lat, lng], { 
                        icon: roleIcons['user'] 
                    }).addTo(map);
                    currentLocationMarker.bindPopup('<strong>Your Current Location</strong>').openPopup();
                },
                function(error) {
                    alert('Unable to get your location: ' + error.message);
                }
            );
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    }

    // Reset map view
    function resetMapView() {
        map.setView([14.5995, 120.9842], 13);
    }

    // Filter markers
    function filterMarkers(type) {
        allMarkers.forEach(marker => {
            if (type === 'all' || marker.markerRole === type) {
                marker.addTo(map);
            } else {
                map.removeLayer(marker);
            }
        });
    }

    @if($role !== 'buyer')
    // Handle map click for non-buyers
    function handleMapClick(e) {
        currentLat = e.latlng.lat;
        currentLng = e.latlng.lng;
        
        // Show modal instead of prompt
        const modal = new bootstrap.Modal(document.getElementById('locationModal'));
        modal.show();
        
        // Preview marker
        if (tempMarker) {
            map.removeLayer(tempMarker);
        }
        tempMarker = L.marker([currentLat, currentLng], { 
            icon: getIconForRole('{{ $role }}') 
        }).addTo(map);
        tempMarker.bindPopup('Click "Set Location" to confirm').openPopup();
    }

    // Confirm location setting
    function confirmLocationSetting() {
        const locationName = document.getElementById('locationNameInput').value.trim();
        if (!locationName) {
            alert('Please enter a location name');
            return;
        }

        // Update form and submit
        document.getElementById('lat').value = currentLat;
        document.getElementById('lng').value = currentLng;
        document.getElementById('name').value = locationName;
        
        // Show loading state
        document.getElementById('confirmLocation').innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Setting Location...';
        document.getElementById('confirmLocation').disabled = true;
        
        document.getElementById('location-form').submit();
    }
    @endif

    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map
        initializeMap();
        
        @if($role !== 'buyer')
        // Add click handler for non-buyers
        map.on('click', handleMapClick);
        
        // Add confirm location handler
        document.getElementById('confirmLocation').addEventListener('click', confirmLocationSetting);
        @endif

        // Add existing saved locations
        @foreach($locations as $location)
            @php
                $lat = is_null($location->latitude) ? null : (float) $location->latitude;
                $lng = is_null($location->longitude) ? null : (float) $location->longitude;
                $r   = $location->user->role ?? 'buyer';
            @endphp
            (function() {
                const lat = @json($lat);
                const lng = @json($lng);
                const role = @json(strtolower($r));
                const html = `
                    <div class="popup-content">
                        <h6 class="fw-bold mb-2" style="color: #2c3e50;">{{ addslashes($location->location_name) }}</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-user text-muted me-2"></i>
                            <small>{{ addslashes($location->user->name ?? 'Unknown') }}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tag text-muted me-2"></i>
                            <small class="badge bg-{{ $r === 'supplier' ? 'success' : ($r === 'reseller' ? 'danger' : 'info') }}">
                                {{ addslashes(ucfirst($r)) }}
                            </small>
                        </div>
                    </div>
                `;
                safeAddMarker(lat, lng, role, html, 'location');
            })();
        @endforeach

        // Add users with coordinates
        @foreach($users as $user)
            @php
                $ulat = is_null($user->latitude) ? null : (float) $user->latitude;
                $ulng = is_null($user->longitude) ? null : (float) $user->longitude;
                $urole = $user->role ?? 'buyer';
            @endphp
            (function() {
                const lat = @json($ulat);
                const lng = @json($ulng);
                const role = @json(strtolower($urole));
                const html = `
                    <div class="popup-content">
                        <h6 class="fw-bold mb-2" style="color: #2c3e50;">{{ addslashes($user->name) }}</h6>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-tag text-muted me-2"></i>
                            <small class="badge bg-{{ $urole === 'supplier' ? 'success' : ($urole === 'reseller' ? 'danger' : 'info') }}">
                                {{ addslashes(ucfirst($urole)) }}
                            </small>
                        </div>
                    </div>
                `;
                safeAddMarker(lat, lng, role, html, 'user');
            })();
        @endforeach
    });
</script>

{{-- Custom marker styles --}}
<style>
    .custom-marker {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
        border: 3px solid white;
    }
    
    .buyer-marker {
        background: linear-gradient(45deg, #17a2b8, #138496);
    }
    
    .reseller-marker {
        background: linear-gradient(45deg, #dc3545, #c82333);
    }
    
    .supplier-marker {
        background: linear-gradient(45deg, #28a745, #218838);
    }
    
    .user-marker {
        background: linear-gradient(45deg, #ffc107, #e0a800);
        color: #212529 !important;
    }
    
    .custom-popup .leaflet-popup-content {
        margin: 0;
        padding: 0;
    }
    
    .popup-content {
        padding: 10px;
        min-width: 200px;
    }
</style>

{{-- Add Font Awesome if not already included --}}
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection