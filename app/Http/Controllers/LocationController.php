<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        // Prevent buyers from adding locations
        if (auth()->user()->role === 'buyer') {
            abort(403, 'Buyers cannot add locations.');
        }

        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_name' => 'required|string|max:255',
            'type' => 'required|in:supply,store',
        ]);

        // Create location entry
        $location = Location::create([
            'user_id' => Auth::id(),
            'location_name' => $request->location_name,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Automatically update user's address field
        $user = Auth::user();
        $user->address = $request->location_name; // You can modify to use reverse geocoded address if available
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return redirect()->back()->with('success', 'Location saved successfully and address updated.');
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

        // Update location
        $location->update([
            'location_name' => $request->location_name,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // Automatically update user's address field
        $user = Auth::user();
        $user->address = $request->location_name;
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return redirect()->back()->with('success', 'Location and address updated successfully.');
    }

    public function destroy(Location $location)
    {
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
