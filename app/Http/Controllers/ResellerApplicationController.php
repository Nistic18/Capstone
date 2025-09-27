<?php
namespace App\Http\Controllers;

use App\Models\ResellerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResellerApplicationController extends Controller
{
    public function create()
    {
    $application = ResellerApplication::where('user_id', Auth::id())->latest()->first();
    return view('reseller.apply', compact('application'));
    }


    public function store(Request $request)
    {
    $request->validate([
        'valid_id'        => 'required|file|mimes:jpg,png,pdf|max:2048',
        'business_permit' => 'required|file|mimes:jpg,png,pdf|max:2048',
        'other_document'  => 'required|file|mimes:jpg,png,pdf|max:2048',
    ]);

    $validIdPath = $request->file('valid_id')->store('reseller_documents', 'public');
    $businessPermitPath = $request->file('business_permit')->store('reseller_documents', 'public');
    $otherPath = $request->file('other_document')->store('reseller_documents', 'public');

    ResellerApplication::create([
        'user_id'          => Auth::id(),
        'valid_id_path'    => $validIdPath,
        'business_path'    => $businessPermitPath,
        'other_doc_path'   => $otherPath,
        'status'           => 'pending',
    ]);

    return redirect()->back()->with('success', 'Your reseller application has been submitted. Please wait for admin approval.');
    }
    
    
}
