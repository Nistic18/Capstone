@extends('layouts.app')

@section('title', 'Apply as Reseller')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
            <h4 class="mb-0"><i class="fas fa-file-upload me-2"></i>Apply as Reseller</h4>
        </div>
        <div class="card-body">

            {{-- ✅ Show success message --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- ✅ If user already applied --}}
            @if($application)

                {{-- Show status --}}
                <div class="alert 
                    @if($application->status === 'pending') alert-warning 
                    @elseif($application->status === 'approved') alert-success 
                    @else alert-danger @endif">
                    <strong>Status:</strong> {{ ucfirst($application->status) }}
                </div>

                {{-- Show uploaded docs --}}
                <div class="mb-3">
                    <p><strong>Valid ID:</strong> 
                        <a href="{{ asset('storage/' . $application->valid_id_path) }}" target="_blank">View</a>
                    </p>
                    <p><strong>Business Permit:</strong> 
                        <a href="{{ asset('storage/' . $application->business_path) }}" target="_blank">View</a>
                    </p>
                    <p><strong>Other Document:</strong> 
                        <a href="{{ asset('storage/' . $application->other_doc_path) }}" target="_blank">View</a>
                    </p>
                </div>

                {{-- ✅ Allow reapply only if rejected --}}
                @if($application->status === 'rejected')
                <div class="alert alert-danger">
                    <strong>Reason for Rejection:</strong> {{ $application->rejection_reason }}
                </div>
                    <hr>
                    <h5 class="fw-bold">Reapply as Reseller</h5>
                    <form action="{{ route('reseller.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="valid_id" class="form-label fw-semibold">Upload Valid ID</label>
                            <input type="file" name="valid_id" id="valid_id" class="form-control" accept=".jpg,.png,.pdf" required>
                        </div>

                        <div class="mb-3">
                            <label for="business_permit" class="form-label fw-semibold">Upload Business Permit</label>
                            <input type="file" name="business_permit" id="business_permit" class="form-control" accept=".jpg,.png,.pdf" required>
                        </div>

                        <div class="mb-3">
                            <label for="other_document" class="form-label fw-semibold">Upload Other Document</label>
                            <input type="file" name="other_document" id="other_document" class="form-control" accept=".jpg,.png,.pdf" required>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-1"></i> Reapply
                        </button>
                    </form>
                @endif

            @else
                {{-- ✅ Show application form if no application exists --}}
                <form action="{{ route('reseller.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="valid_id" class="form-label fw-semibold">Upload Valid ID</label>
                        <input type="file" name="valid_id" id="valid_id" class="form-control" accept=".jpg,.png,.pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="business_permit" class="form-label fw-semibold">Upload Business Permit</label>
                        <input type="file" name="business_permit" id="business_permit" class="form-control" accept=".jpg,.png,.pdf" required>
                    </div>

                    <div class="mb-3">
                        <label for="other_document" class="form-label fw-semibold">Upload Other Document</label>
                        <input type="file" name="other_document" id="other_document" class="form-control" accept=".jpg,.png,.pdf" required>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-1"></i> Submit Application
                    </button>
                </form>
            @endif

        </div>
    </div>
</div>
@endsection
