<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ResellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Get role filter from request
        $roleFilter = $request->get('role');
        
        // Build query with role filter
        $query = User::with('latestResellerApplication')->latest();
        
        // Apply role filter if provided
        if ($roleFilter && in_array($roleFilter, ['admin', 'buyer', 'reseller', 'supplier'])) {
            $query->where('role', $roleFilter);
        }
        
        // Paginate results
        $users = $query->paginate(10)->appends(['role' => $roleFilter]);
        
        // Also get pending supplier applications
        $pendingApplications = ResellerApplication::where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();
            
        return view('users.index', compact('users', 'pendingApplications', 'roleFilter'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,buyer,reseller,supplier',
            'password' => 'nullable|min:8',
            'phone' => 'nullable|string|max:11',
            'barangay' => 'nullable|string',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        
        // Update address with barangay if provided
        if (!empty($validated['barangay'])) {
            $user->address = $validated['barangay'] . ', Rosario, Cavite';
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    
    /**
     * Approve supplier application and create account
     */
    public function approveSupplier(Request $request, $applicationId)
    {
        $application = ResellerApplication::findOrFail($applicationId);
        
        // Check if application is already processed
        if ($application->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }
        
        // Check if user with this email already exists
        $existingUser = User::where('email', $application->email_address)->first();
        
        $defaultPassword = null;
        
        if ($existingUser) {
            // If user exists, just update their role to supplier
            $existingUser->update(['role' => 'supplier']);
            $user = $existingUser;
            $defaultPassword = null; // Existing user keeps their password
        } else {
            // Create new supplier account with default password
            $name = $application->business_name;
            // Generate default password: name@1234
            $defaultPassword = Str::slug($name) . '@1234';
            
            $user = User::create([
                'name' => $name,
                'email' => $application->email_address,
                'password' => Hash::make($defaultPassword),
                'role' => 'supplier',
                'address' => $application->address . ', ' . $application->city . ', ' . $application->province . ' ' . $application->zip_code,
            ]);
        }
        
        // Update application status
        $application->update([
            'status' => 'approved',
            'user_id' => $user->id,
        ]);
        
        // Send notification email
        try {
            Mail::to($user->email)->send(new \App\Mail\SupplierApplicationApproved($user, $defaultPassword));
        } catch (\Exception $e) {
            Log::error('Failed to send approval email: ' . $e->getMessage());
            // Continue even if email fails
        }
        
        $message = $defaultPassword 
            ? 'Supplier application approved successfully! Account created with default password and email sent.'
            : 'Supplier application approved successfully! User role updated to supplier and email sent.';
            
        return back()->with('success', $message);
    }
    
    /**
     * Reject supplier application
     */
    public function rejectSupplier(Request $request, $applicationId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        $application = ResellerApplication::findOrFail($applicationId);
        
        if ($application->status !== 'pending') {
            return back()->with('error', 'This application has already been processed.');
        }
        
        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        // Send rejection notification (optional)
        try {
            Mail::to($application->email_address)->send(new \App\Mail\SupplierApplicationRejected($application));
        } catch (\Exception $e) {
            Log::error('Failed to send rejection email: ' . $e->getMessage());
        }
        
        return back()->with('success', 'Supplier application rejected.');
    }
}