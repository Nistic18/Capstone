<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class LocationController extends Controller
{
public function store(Request $request)
{
    if (auth()->user()->role === 'buyer') {
        abort(403, 'Buyers cannot add locations.');
    }
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
    $locations = Location::with('user')->get();
    $role = auth()->check() ? auth()->user()->role : null;

    // Fetch users with lat/lng
    $users = User::whereNotNull('latitude')
                 ->whereNotNull('longitude')
                 ->get();

    return view('map', compact('locations', 'role', 'users'));
}
public function index()
{
    $locations = Location::with('user')->get();
    $users = User::whereNotNull('latitude')
                 ->whereNotNull('longitude')
                 ->get();

    return view('map', compact('locations', 'users'));
}

}
