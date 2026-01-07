<?php

namespace App\Http\Controllers;

use App\Models\ResellerApplication;
use App\Models\User;
use App\Mail\ResellerApplicationSubmitted;
use App\Mail\SupplierApplicationApproved;
use App\Mail\SupplierApplicationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResellerApplicationController extends Controller
{
    
    /**
     * Show the reseller application form.
     */
    public function create()
    {
        // Check if the authenticated user already has an application
        $application = null;
        
        if (auth()->check()) {
            $application = ResellerApplication::where('email_address', auth()->user()->email)
                ->latest()
                ->first();
        }

        return view('reseller.apply', compact('application'));
    }

    /**
     * Handle submission of reseller application.
     */
    public function store(Request $request)
    {
        // ✅ Validate input fields
        $validated = $request->validate([
            'email_address'       => 'required|email',
            'business_name'       => 'required|string|max:255',
            'address'             => 'required|string|max:255',
            'street_address'      => 'required|string|max:255',
            'barangay'            => 'required|string|max:100',
            'business_license_id' => 'required|string|max:100',
            'phone_number'        => 'nullable|string|max:20',
            
            // Document uploads
            'business_permit'     => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'sanitation_cert'     => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'govt_id_1'           => 'required|image|mimes:jpg,jpeg,png|max:10240',
            'govt_id_2'           => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        // ✅ Check if this email already has a pending application
        $existing = ResellerApplication::where('email_address', $request->email_address)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            if ($existing->status == 'approved') {
                return back()->with('error', 'You are already an approved reseller.');
            }
            return back()->with('error', 'You already have a pending reseller application.');
        }

        // ✅ Store uploaded images
// Destination folders
$businessPermitPath = $_SERVER['DOCUMENT_ROOT'] . '/img/reseller_applications/business_permits';
$sanitationCertPath  = $_SERVER['DOCUMENT_ROOT'] . '/img/reseller_applications/sanitation_certs';
$govtIdPath          = $_SERVER['DOCUMENT_ROOT'] . '/img/reseller_applications/govt_ids';

// Create folders if they don't exist
foreach ([$businessPermitPath, $sanitationCertPath, $govtIdPath] as $folder) {
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
}

// Move uploaded files
$businessPermitFilename = time().'_'.uniqid().'.'.$request->file('business_permit')->getClientOriginalExtension();
$request->file('business_permit')->move($businessPermitPath, $businessPermitFilename);

$sanitationCertFilename = time().'_'.uniqid().'.'.$request->file('sanitation_cert')->getClientOriginalExtension();
$request->file('sanitation_cert')->move($sanitationCertPath, $sanitationCertFilename);

$govtId1Filename = time().'_'.uniqid().'.'.$request->file('govt_id_1')->getClientOriginalExtension();
$request->file('govt_id_1')->move($govtIdPath, $govtId1Filename);

$govtId2Filename = time().'_'.uniqid().'.'.$request->file('govt_id_2')->getClientOriginalExtension();
$request->file('govt_id_2')->move($govtIdPath, $govtId2Filename);

// Store relative paths in database
$businessPermitPath = 'img/reseller_applications/business_permits/'.$businessPermitFilename;
$sanitationCertPath = 'img/reseller_applications/sanitation_certs/'.$sanitationCertFilename;
$govtId1Path       = 'img/reseller_applications/govt_ids/'.$govtId1Filename;
$govtId2Path       = 'img/reseller_applications/govt_ids/'.$govtId2Filename;


        // ✅ Create new reseller application
        $application = ResellerApplication::create([
            'email_address'       => $validated['email_address'],
            'business_name'       => $validated['business_name'],
            'address'             => $validated['address'],
            'business_license_id' => $validated['business_license_id'],
            'phone_number'        => $validated['phone_number'],
            
            // Store document paths
            'business_permit_photo' => $businessPermitPath,
            'sanitation_cert_photo' => $sanitationCertPath,
            'govt_id_photo_1'       => $govtId1Path,
            'govt_id_photo_2'       => $govtId2Path,
            
            'status'              => 'pending',
        ]);

        // ✅ Send confirmation email to applicant
        try {
            Mail::to($application->email_address)
                ->send(new ResellerApplicationSubmitted($application));
        } catch (\Exception $e) {
            // Log the error but don't stop the application process
            Log::error('Failed to send reseller application email: ' . $e->getMessage());
        }

        return redirect()->route('reseller.create')->with('success', 'Your Supplier application has been submitted successfully. Please check your email for confirmation.');
    }

    /**
     * Approve a reseller application.
     */
    public function approve($id)
    {
        $application = ResellerApplication::findOrFail($id);

        // Check if already approved
        if ($application->status === 'approved') {
            return back()->with('error', 'This application has already been approved.');
        }

        // Check if user already exists
        $user = User::where('email', $application->email_address)->first();
        $defaultPassword = null;

        if (!$user) {
            // Create new user account
            $defaultPassword = Str::random(12); // Generate random password
            
            $user = User::create([
                'name' => $application->business_name,
                'email' => $application->email_address,
                'password' => Hash::make($defaultPassword),
                'role' => 'reseller', // or 'supplier' based on your user roles
                'phone' => $application->phone_number,
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
            Mail::to($application->email_address)
                ->send(new SupplierApplicationApproved($user, $defaultPassword));
        } catch (\Exception $e) {
            Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return back()->with('success', 'Application approved successfully. User has been notified via email.');
    }

    /**
     * Reject a reseller application.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $application = ResellerApplication::findOrFail($id);

        // Check if already rejected
        if ($application->status === 'rejected') {
            return back()->with('error', 'This application has already been rejected.');
        }

        // Update application status
        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
        ]);

        // Send rejection email
        try {
            Mail::to($application->email_address)
                ->send(new SupplierApplicationRejected($application));
        } catch (\Exception $e) {
            Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return back()->with('success', 'Application rejected. Applicant has been notified via email.');
    }

    /**
     * Allow user to reset their rejected application and apply again
     */
    public function resetApplication(Request $request)
    {
        $user = auth()->user();
        
        // Get the user's application
        $application = ResellerApplication::where('email_address', $user->email)->first();
        
        // Check if application exists and is rejected
        if (!$application || $application->status !== 'rejected') {
            return redirect()->route('reseller.create')
                ->with('error', 'No rejected application found.');
        }
        
        // Check if 3 days have passed since rejection
        $rejectedDate = \Carbon\Carbon::parse($application->rejected_at ?? $application->updated_at);
        $canReapplyDate = $rejectedDate->addDays(3);
        $now = \Carbon\Carbon::now();
        
        if ($now->lessThan($canReapplyDate)) {
            $daysRemaining = $now->diffInDays($canReapplyDate, false) + 1;
            return redirect()->route('reseller.create')
                ->with('error', "You must wait {$daysRemaining} more day(s) before reapplying. You can apply again on {$canReapplyDate->format('F d, Y')}.");
        }
        
        // Delete the old application (or you can soft delete/archive it)
        // If you want to keep history, consider updating status to 'archived' instead of deleting
        $application->delete();
        
        return redirect()->route('reseller.create')
            ->with('success', 'You can now submit a new application.');
    }
}