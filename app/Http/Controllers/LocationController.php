<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Geocode an address to get lat/lng coordinates
     */
    private function geocodeAddress($address)
    {
        try {
            // Using Nominatim (OpenStreetMap) API
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'q' => $address,
                'format' => 'json',
                'limit' => 1,
                'countrycodes' => 'ph', // Restrict to Philippines
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $result = $response->json()[0];
                return [
                    'latitude' => (float) $result['lat'],
                    'longitude' => (float) $result['lon']
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding failed: ' . $e->getMessage());
        }

        return null;
    }

    public function store(Request $request)
{
    // Prevent buyers from adding locations
    if (auth()->user()->role === 'buyer') {
        abort(403, 'Buyers cannot add locations.');
    }

    // Check if supplier already has a location
    if (auth()->user()->role === 'supplier') {
        $existingLocationCount = Location::where('user_id', Auth::id())->count();
        if ($existingLocationCount >= 1) {
            return redirect()->back()->with('error', 'Suppliers can only add one location. Please edit or delete your existing location.');
        }
    }

    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'location_name' => 'required|string|max:255',
        'type' => 'required|in:supply,store',
    ]);

    // Create location entry ONLY - don't touch user's address
    $location = Location::create([
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

        // Update location ONLY - don't touch user's address
        $location->update([
            'location_name' => $request->location_name,
            'type' => $request->type,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        // REMOVED: Automatic address update
        // Keep user's actual address separate from location names

        return redirect()->back()->with('success', 'Location updated successfully.');
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

        // Get all users
        $users = User::all();

        return view('map', compact('locations', 'role', 'users', 'userLocations'));
    }

    public function index()
    {
        $locations = Location::with('user')->get();
        $userLocations = auth()->check() ? Location::where('user_id', Auth::id())->get() : collect();
        
        // Get all users (addresses will be geocoded on frontend)
        $users = User::all();

        return view('map', compact('locations', 'users', 'userLocations'));
    }

    /**
     * API endpoint to save geocoded coordinates for a user
     */
    public function saveGeocodedAddress(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Only allow updating own coordinates or if admin
        if (Auth::id() !== (int)$request->user_id && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $user = User::find($request->user_id);
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return response()->json(['success' => true]);
    }
public function getSupplierLocations()
{
    // Get all locations where the user is a supplier
    $locations = Location::with(['user' => function($query) {
        $query->select('id', 'name', 'email', 'phone', 'role', 'address');
    }])
    ->whereHas('user', function($query) {
        $query->where('role', 'supplier');
    })
    ->select('id', 'user_id', 'location_name', 'type', 'latitude', 'longitude')
    ->get();

    return response()->json($locations);
}

/**
 * Alternative: If you want to include ALL locations (not just suppliers)
 * Use this method instead
 */
public function getAllLocations()
{
    $locations = Location::with(['user' => function($query) {
        $query->select('id', 'name', 'email', 'phone', 'role');
    }])
    ->select('id', 'user_id', 'location_name', 'type', 'latitude', 'longitude')
    ->get();

    return response()->json($locations);
}
}