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
            'country'             => 'required|string|max:100',
            'province'            => 'required|string|max:100',
            'city'                => 'required|string|max:100',
            'zip_code'            => 'required|string|max:20',
            'business_license_id' => 'required|string|max:100',
            'phone_number'        => 'nullable|string|max:20',
            'pdf_file'            => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // up to 10MB
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

        // ✅ Store uploaded PDF
        $pdfPath = $request->file('pdf_file')->store('reseller_applications', 'public');

        // ✅ Create new reseller application
        $application = ResellerApplication::create([
            'email_address'       => $validated['email_address'],
            'business_name'       => $validated['business_name'],
            'address'             => $validated['address'],
            'country'             => $validated['country'],
            'province'            => $validated['province'],
            'city'                => $validated['city'],
            'zip_code'            => $validated['zip_code'],
            'business_license_id' => $validated['business_license_id'],
            'phone_number'        => $validated['phone_number'],
            'pdf_file'            => $pdfPath,
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
}