<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ResellerApplication;
use App\Models\UserBanHistory;
use App\Mail\SupplierApplicationApproved;
use App\Mail\SupplierApplicationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        
        // Get ban history
        $banHistory = UserBanHistory::with('user', 'bannedBy')
            ->latest()
            ->paginate(10, ['*'], 'ban_page');
            
        return view('users.index', compact('users', 'pendingApplications', 'roleFilter', 'banHistory'));
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

    /**
     * Restrict user for 2 days
     */
    public function restrictUser(Request $request, User $user)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            // Set restriction until 2 days from now
            $restrictedUntil = Carbon::now()->addDays(2);
            
            $user->update([
                'is_banned' => true,
                'banned_until' => $restrictedUntil,
                'ban_reason' => $request->reason,
            ]);

            // Create ban history record
            UserBanHistory::create([
                'user_id' => $user->id,
                'banned_by' => auth()->id(),
                'action_type' => 'restrict',
                'reason' => $request->reason,
                'banned_until' => $restrictedUntil,
            ]);

            return back()->with('success', "User {$user->name} has been restricted for 2 days.");
        } catch (\Exception $e) {
            Log::error('Error restricting user: ' . $e->getMessage());
            return back()->with('error', 'Failed to restrict user. Please try again.');
        }
    }

    /**
     * Ban user permanently
     */
    public function banUser(Request $request, User $user)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            $user->update([
                'is_banned' => true,
                'banned_until' => null, // null means permanent ban
                'ban_reason' => $request->reason,
            ]);

            // Create ban history record
            UserBanHistory::create([
                'user_id' => $user->id,
                'banned_by' => auth()->id(),
                'action_type' => 'ban',
                'reason' => $request->reason,
                'banned_until' => null,
            ]);

            return back()->with('success', "User {$user->name} has been permanently banned.");
        } catch (\Exception $e) {
            Log::error('Error banning user: ' . $e->getMessage());
            return back()->with('error', 'Failed to ban user. Please try again.');
        }
    }

    /**
     * Unban/unrestrict user
     */
    public function unbanUser(User $user)
    {
        try {
            $user->update([
                'is_banned' => false,
                'banned_until' => null,
                'ban_reason' => null,
            ]);

            return back()->with('success', "User {$user->name} has been unbanned.");
        } catch (\Exception $e) {
            Log::error('Error unbanning user: ' . $e->getMessage());
            return back()->with('error', 'Failed to unban user. Please try again.');
        }
    }
    
    /**
     * Approve supplier application and create account
     */
    public function approveSupplier(Request $request, $applicationId)
    {
        try {
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
                $existingUser->update([
                    'role' => 'supplier',
                    'email_verified_at' => now()
                ]);
                $user = $existingUser;
            } else {
                // Create new supplier account with default password
                $name = $application->business_name;
                // Generate secure random password
                $defaultPassword = Str::random(12);
                
                $user = User::create([
                    'name' => $name,
                    'email' => $application->email_address,
                    'password' => Hash::make($defaultPassword),
                    'role' => 'supplier',
                    'phone' => $application->phone_number,
                    'address' => $application->address,
                    'email_verified_at' => now(), // Auto-verify email
                ]);
            }
            
            // Update application status
            $application->update([
                'status' => 'approved',
                'approved_at' => now(),
                'user_id' => $user->id,
            ]);
            
            // Send approval email
            try {
                Log::info('Attempting to send approval email to: ' . $application->email_address);
                
                Mail::to($application->email_address)
                    ->send(new SupplierApplicationApproved($user, $defaultPassword));
                
                Log::info('Approval email sent successfully to: ' . $application->email_address);
            } catch (\Exception $e) {
                Log::error('Failed to send approval email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                // Don't fail the approval, just notify admin
                return back()->with('warning', 'Application approved successfully, but failed to send email notification. Please contact the supplier directly.');
            }
            
            $message = $defaultPassword 
                ? 'Supplier application approved successfully! Account created with default password and email sent.'
                : 'Supplier application approved successfully! User role updated to supplier and email sent.';
                
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Error approving supplier application: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve application. Please try again.');
        }
    }
    
    /**
     * Reject supplier application
     */
    public function rejectSupplier(Request $request, $applicationId)
    {
        try {
            $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);
            
            $application = ResellerApplication::findOrFail($applicationId);
            
            if ($application->status !== 'pending') {
                return back()->with('error', 'This application has already been processed.');
            }
            
            // Update application status
            $application->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'rejected_at' => now(),
            ]);
            
            // Send rejection email
            try {
                Log::info('Attempting to send rejection email to: ' . $application->email_address);
                
                Mail::to($application->email_address)
                    ->send(new SupplierApplicationRejected($application));
                
                Log::info('Rejection email sent successfully to: ' . $application->email_address);
            } catch (\Exception $e) {
                Log::error('Failed to send rejection email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                // Don't fail the rejection, just notify admin
                return back()->with('warning', 'Application rejected successfully, but failed to send email notification. Please contact the applicant directly.');
            }
            
            return back()->with('success', 'Supplier application rejected successfully. Applicant has been notified via email.');
            
        } catch (\Exception $e) {
            Log::error('Error rejecting supplier application: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject application. Please try again.');
        }
    }
}