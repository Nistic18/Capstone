<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResellerApplication;
use App\Mail\ResellerApplicationApproved;
use App\Mail\ResellerApplicationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminResellerController extends Controller
{
    /**
     * Display a listing of reseller applications.
     */
    public function index()
    {
        $applications = ResellerApplication::latest()->paginate(20);
        return view('admin.reseller.index', compact('applications'));
    }

    /**
     * Show the details of a specific application.
     */
    public function show($id)
    {
        $application = ResellerApplication::findOrFail($id);
        return view('admin.reseller.show', compact('application'));
    }

    /**
     * Approve a reseller application.
     */
    public function approve($id)
    {
        $application = ResellerApplication::findOrFail($id);
        
        if ($application->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be approved.');
        }

        $application->update([
            'status' => 'approved',
            'reviewed_at' => now()
        ]);

        // Send approval email
        try {
            Mail::to($application->email_address)
                ->send(new ResellerApplicationApproved($application));
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return back()->with('success', 'Application approved successfully. Approval email sent to ' . $application->email_address);
    }

    /**
     * Reject a reseller application.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $application = ResellerApplication::findOrFail($id);
        
        if ($application->status !== 'pending') {
            return back()->with('error', 'Only pending applications can be rejected.');
        }

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now()
        ]);

        // Send rejection email
        try {
            Mail::to($application->email_address)
                ->send(new ResellerApplicationRejected($application));
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return back()->with('success', 'Application rejected. Rejection email sent to ' . $application->email_address);
    }
}