@extends('layouts.app')

@section('content')
<div class="card card-body">
    <h2 class="fw-bold mb-3 text-primary">Set Supply/Store Location</h2>

    <div id="map" style="height: 500px;"></div>

    <form method="POST" action="{{ route('locations.store') }}">
        @csrf
        <input type="hidden" id="lat" name="latitude">
        <input type="hidden" id="lng" name="longitude">
        <button type="submit" class="btn btn-primary mt-3">Save Location</button>
    </form>
</div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    var map = L.map('map').setView([14.5995, 120.9842], 13); // Manila default view

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: ''
    }).addTo(map);

    let marker;

    // On map click, allow setting a new marker
    map.on('click', function(e) {
        const { lat, lng } = e.latlng;

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }

        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
    });

    // Loop through existing saved locations and render markers
    @foreach($locations as $location)
        L.marker([{{ $location->latitude }}, {{ $location->longitude }}])
            .addTo(map)
            .bindPopup("{{ ucfirst($location->type) }} Location");
    @endforeach
</script>
@endsection
