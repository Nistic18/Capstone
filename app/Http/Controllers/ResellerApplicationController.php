<?php

namespace App\Http\Controllers;

use App\Models\ResellerApplication;
use App\Mail\ResellerApplicationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
        $businessPermitPath = $request->file('business_permit')->store('reseller_applications/business_permits', 'public');
        $sanitationCertPath = $request->file('sanitation_cert')->store('reseller_applications/sanitation_certs', 'public');
        $govtId1Path = $request->file('govt_id_1')->store('reseller_applications/govt_ids', 'public');
        $govtId2Path = $request->file('govt_id_2')->store('reseller_applications/govt_ids', 'public');

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
 * Allow user to reset their rejected application and apply again
 */
public function resetApplication(Request $request)
{
    $user = auth()->user();
    
    // Get the user's application
    $application = ResellerApplication::where('user_id', $user->id)->first();
    
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