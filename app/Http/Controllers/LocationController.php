<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class LocationController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'location_name' => 'required|string|max:255',
        'type' => 'required|in:supply,store',
    ]);

    Location::create([
        'user_id' => Auth::id(),
        'location_name' => $request->location_name,
        'type' => $request->type,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
    ]);

    return redirect()->back()->with('success', 'Location saved.');
}
public function showMap()
{
    // Get all locations, or filter by user if needed
    $locations = Location::all(); // or Location::where('user_id', Auth::id())->get();

    return view('map', compact('locations'));
}
}
