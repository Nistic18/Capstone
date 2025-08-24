@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center d-flex justify-content-center">
                    <div class="card-header">{{ __('Register') }}</div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Name --}}
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                       name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- Confirm Password --}}
                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control"
                                       name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        {{-- Location --}}
                        <div class="row mb-3">
                            <label for="location" class="col-md-4 col-form-label text-md-end">{{ __('Location') }}</label>
                            <div class="col-md-6">
                                <div id="map" style="height: 300px; border:1px solid #ddd; border-radius:6px;"></div>
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                                <small class="text-muted">Click on the map to set your location</small>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-info" onclick="getCurrentLocation()" style="border-radius: 15px;">
                                <i class="fas fa-location-arrow me-1"></i>My Location
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetMapView()" style="border-radius: 15px;">
                                <i class="fas fa-search-location me-1"></i>Reset View
                            </button>
                        </div>

                        {{-- Submit --}}
                        <div class="row mb-0 mt-3">
                            <div class="col-md-6 offset-md-4 text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                                <a href="{{ route('login') }}" class="btn btn-link">{{ __('Login') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leaflet CSS & JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Default: Manila
    var defaultLat = 14.5995;
    var defaultLng = 120.9842;
    var defaultZoom = 13;

    var map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: ''
    }).addTo(map);

    let marker;

    // Click on map to set location
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;
        setMarker(lat, lng);
    });

    // Set marker and update hidden inputs
    function setMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }

    // Get current location
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                setMarker(lat, lng);
                map.setView([lat, lng], 16);
            }, function() {
                alert("Unable to retrieve your location.");
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    // Reset to default view
    function resetMapView() {
        map.setView([defaultLat, defaultLng], defaultZoom);
        if (marker) {
            map.removeLayer(marker);
            marker = null;
        }
        document.getElementById('latitude').value = "";
        document.getElementById('longitude').value = "";
    }
</script>
@endsection
