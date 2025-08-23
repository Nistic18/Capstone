@php
    $role = auth()->check() ? auth()->user()->role : null;
@endphp

@extends('layouts.app')

@section('content')
<div class="card card-body">
    <h2 class="fw-bold mb-3 text-primary">
        {{ $role === 'buyer' ? 'View Supply/Store Locations' : 'Set Supply/Store Location' }}
    </h2>

    <div id="map" style="height: 500px;"></div>

    @if($role !== 'buyer')
    <form id="location-form" method="POST" action="{{ route('locations.store') }}">
        @csrf
        <input type="hidden" id="lat" name="latitude">
        <input type="hidden" id="lng" name="longitude">
        <input type="hidden" id="name" name="location_name">
        <input type="hidden" name="type" value="store">
    </form>
    @endif
</div>

<!-- Leaflet Core CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Geocoder Plugin -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    const map = L.map('map').setView([14.5995, 120.9842], 13); // Manila

    // OSM tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '' }).addTo(map);

    // Role-based icons (blue / red / green)
    const roleIcons = {
        buyer: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        }),
        reseller: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
        }),
        supplier: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
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

    function safeAddMarker(lat, lng, role, popupHTML) {
        try {
            if (!isValidLatLng(lat, lng)) {
                console.warn('Skipped invalid lat/lng:', lat, lng, role);
                return null;
            }
            const m = L.marker([lat, lng], { icon: getIconForRole(role) }).addTo(map);
            if (popupHTML) m.bindPopup(popupHTML);
            return m;
        } catch (e) {
            console.error('Failed to add marker:', e);
            return null;
        }
    }

    // Geocoder
    const geocoder = L.Control.Geocoder.nominatim({ geocodingQueryParams: { countrycodes: 'ph' } });
    L.Control.geocoder({ defaultMarkGeocode: false, geocoder })
        .on('markgeocode', function(e) { map.setView(e.geocode.center, 18); })
        .addTo(map);

    let tempMarker = null;

    @if($role !== 'buyer')
    // Only non-buyers can add locations (marker shows before submit)
    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        const locationName = prompt("Enter store/supply name:");
        if (!locationName) return;

        if (tempMarker) {
            tempMarker.setLatLng([lat, lng]).bindPopup(locationName).openPopup();
        } else {
            tempMarker = safeAddMarker(lat, lng, '{{ $role }}', locationName);
        }

        // Fill form + submit (remove this submit if you want to stay on page)
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('name').value = locationName;
        document.getElementById('location-form').submit();
    });
    @endif

    // Existing saved locations
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
                <strong>{{ addslashes($location->location_name) }}</strong><br>
                <small>Added by: {{ addslashes($location->user->name ?? 'Unknown') }} ({{ addslashes($r) }})</small>
            `;
            safeAddMarker(lat, lng, role, html);
        })();
    @endforeach

    // Users (with lat/lng)
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
                <strong>User: {{ addslashes($user->name) }}</strong><br>
                <small>Role: {{ addslashes($urole) }}</small>
            `;
            safeAddMarker(lat, lng, role, html);
        })();
    @endforeach

    // Legend
    const legend = L.control({ position: "bottomright" });
    legend.onAdd = function () {
        const div = L.DomUtil.create("div", "info legend");
        div.innerHTML = `
            <h6 style="margin:0 0 4px 0;"><strong>Legend</strong></h6>
            <p style="margin:4px 0;"><img style="vertical-align:middle" src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png"> Buyer</p>
            <p style="margin:4px 0;"><img style="vertical-align:middle" src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png"> Reseller</p>
            <p style="margin:4px 0;"><img style="vertical-align:middle" src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png"> Supplier</p>
        `;
        div.style.background = "white";
        div.style.padding = "8px";
        div.style.borderRadius = "6px";
        div.style.boxShadow = "0 0 8px rgba(0,0,0,0.3)";
        return div;
    };
    legend.addTo(map);
</script>
@endsection
