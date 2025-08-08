@extends('layouts.app')

@section('content')
<div class="card card-body">
    <h2 class="fw-bold mb-3 text-primary">Set Supply/Store Location</h2>

    <div id="map" style="height: 500px;"></div>

    <form id="location-form" method="POST" action="{{ route('locations.store') }}">
        @csrf
        <input type="hidden" id="lat" name="latitude">
        <input type="hidden" id="lng" name="longitude">
        <input type="hidden" id="name" name="location_name">
        <input type="hidden" name="type" value="store">
    </form>
</div>

<!-- Leaflet Core CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet Geocoder Plugin -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    var map = L.map('map').setView([14.5995, 120.9842], 13); // Default: Manila

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: ''
    }).addTo(map);

    // Add search control to the map
    L.Control.geocoder({
        defaultMarkGeocode: false
    })
    .on('markgeocode', function(e) {
        const latlng = e.geocode.center;
        map.setView(latlng, 15);
    })
    .addTo(map);

    let marker;

    // Handle map clicks to add location
    map.on('click', function (e) {
        const { lat, lng } = e.latlng;

        const locationName = prompt("Enter store/supply name:");
        if (!locationName) return;

        if (marker) {
            marker.setLatLng([lat, lng]).bindPopup(locationName).openPopup();
        } else {
            marker = L.marker([lat, lng]).addTo(map).bindPopup(locationName).openPopup();
        }

        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('name').value = locationName;

        document.getElementById('location-form').submit();
    });

    // Load existing saved locations and keep their popups open
    @foreach($locations as $location)
        L.marker([{{ $location->latitude }}, {{ $location->longitude }}])
            .addTo(map)
            .bindPopup("{{ $location->location_name }}")
            .openPopup();
    @endforeach
</script>
@endsection
