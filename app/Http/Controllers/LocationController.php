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

        return redirect()->back()->with('success', 'Location saved successfully.');
    }

    public function update(Request $request, Location $location)
    {
        // Check if user owns this location or has permission
        if ($location->user_id !== Auth::id() && auth()->user()->role !== 'admin') {
            abort(403, 'You can only edit your own locations.');
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'required|string|max:255',
            'type' => 'required|in:supply,store',
        ]);

        $location->update([
            'location_name' => $request->location_name,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->back()->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        // Check if user owns this location or has permission
        if ($location->user_id !== Auth::id() && auth()->user()->role !== 'admin') {
            abort(403, 'You can only delete your own locations.');
        }

        $location->delete();

        return redirect()->back()->with('success', 'Location deleted successfully.');
    }

    public function showMap()
    {
        $locations = Location::with('user')->get();
        $role = auth()->check() ? auth()->user()->role : null;
        $userLocations = auth()->check() ? Location::where('user_id', Auth::id())->get() : collect();

        // Fetch users with lat/lng
        $users = User::whereNotNull('latitude')
                     ->whereNotNull('longitude')
                     ->get();

        return view('map', compact('locations', 'role', 'users', 'userLocations'));
    }

    public function index()
    {
        $locations = Location::with('user')->get();
        $userLocations = auth()->check() ? Location::where('user_id', Auth::id())->get() : collect();
        $users = User::whereNotNull('latitude')
                     ->whereNotNull('longitude')
                     ->get();

        return view('map', compact('locations', 'users', 'userLocations'));
    }
}