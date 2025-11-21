@extends('layouts.app')

@section('title', 'Apply as Reseller')
@section('content')
<div class="container mt-4 mb-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header text-white text-center py-3 rounded-top-4"
             style="background: linear-gradient(135deg, #088a50 0%, #0bb364 100%);">
            <h3 class="mb-0">Business Registration</h3>
            <p class="mb-0 mt-1 small">Apply to become an authorized Supplier</p>
        </div>

        <div class="card-body p-4">

            {{-- ✅ Show success / error messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(isset($application))
                {{-- ✅ If the user already applied --}}
                <div class="text-center py-4">
                    @if($application->status == 'pending')
                        <div class="mb-3">
                            <i class="bi bi-clock-history text-warning" style="font-size: 3.5rem;"></i>
                        </div>
                        <h4 class="mb-3">Application Under Review</h4>
                        <p class="text-muted mb-3">Your reseller application is currently being reviewed by our team.</p>
                        <div class="alert alert-info d-inline-block">
                            <strong>Status:</strong> <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                    @elseif($application->status == 'approved')
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                        </div>
                        <h4 class="mb-3">Congratulations!</h4>
                        <p class="text-muted mb-3">Your reseller application has been approved. You can now access reseller features.</p>
                        <div class="alert alert-success d-inline-block">
                            <strong>Status:</strong> <span class="badge bg-success">Approved</span>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('home') }}" class="btn btn-primary px-4">Go to Dashboard</a>
                        </div>
                    @elseif($application->status == 'rejected')
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 3.5rem;"></i>
                        </div>
                        <h4 class="mb-3">Application Rejected</h4>
                        <p class="text-muted mb-3">Unfortunately, your reseller application has been rejected.</p>
                        @if($application->rejection_reason)
                            <div class="alert alert-danger">
                                <strong>Reason:</strong> {{ $application->rejection_reason }}
                            </div>
                        @endif
                        <p class="text-muted small">Please contact our support team for more details.</p>
                        <div class="mt-3">
                            <a href="mailto:support@yourstore.com" class="btn btn-outline-primary px-4">Contact Support</a>
                        </div>
                    @endif
                </div>
            @else
                {{-- ✅ Multi-Step Progress Indicator --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center position-relative">
                        <div class="progress-line"></div>
                        
                        <div class="step-item text-center" data-step="1">
                            <div class="step-circle active">
                                <span>1</span>
                            </div>
                            <small class="step-label">Basic information</small>
                        </div>
                        
                        <div class="step-item text-center" data-step="2">
                            <div class="step-circle">
                                <span>2</span>
                            </div>
                            <small class="step-label">Verify business</small>
                        </div>
                        
                        <div class="step-item text-center" data-step="3">
                            <div class="step-circle">
                                <span>3</span>
                            </div>
                            <small class="step-label">Review & Submit</small>
                        </div>
                    </div>
                </div>

                {{-- ✅ Multi-Step Form --}}
                <form action="{{ route('reseller.store') }}" method="POST" enctype="multipart/form-data" id="resellerForm">
                    @csrf

                    {{-- STEP 1: Basic Information --}}
                    <div class="form-step active" data-step="1">
                        <h5 class="mb-3 text-primary">Basic Information</h5>

                        <div class="mb-3">
                            <label for="email_address" class="form-label fw-bold">Registered Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email_address" id="email_address"
                                   class="form-control @error('email_address') is-invalid @enderror"
                                   placeholder="Enter your registered buyer email"
                                   value="{{ old('email_address', auth()->user()->email ?? '') }}" required>
                            @error('email_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="business_name" class="form-label fw-bold">Legal Business Name <span class="text-danger">*</span></label>
                            <input type="text" name="business_name" id="business_name"
                                   class="form-control @error('business_name') is-invalid @enderror"
                                   placeholder="Business name"
                                   value="{{ old('business_name') }}" required>
                            <small class="text-muted">Enter the legal business name exactly as it appears on the business qualifications.</small>
                            @error('business_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" id="phone_number"
                                   class="form-control @error('phone_number') is-invalid @enderror"
                                   placeholder="Business phone number (e.g., 09123456789)"
                                   value="{{ old('phone_number') }}" 
                                   pattern="^(09|\+639)\d{9}$"
                                   maxlength="13"
                                   required>
                            <small class="text-muted">Enter a valid Philippine mobile number.</small>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-geo-alt-fill me-1"></i>Address Details <span class="text-danger">*</span>
                            </label>
                            
                            <!-- Street Address -->
                            <input type="text" 
                                   name="street_address" 
                                   id="street_address"
                                   class="form-control mb-2 @error('street_address') is-invalid @enderror" 
                                   placeholder="Street (e.g., Phase 1 Blk 1 Lot 1)"
                                   value="{{ old('street_address') }}"
                                   required>
                            @error('street_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Barangay Dropdown -->
                            <select name="barangay" 
                                    id="barangay"
                                    class="form-select @error('barangay') is-invalid @enderror" 
                                    required>
                                <option value="" selected>Select Barangay</option>
                                <option value="Bagbag I" {{ old('barangay') == 'Bagbag I' ? 'selected' : '' }}>Bagbag I</option>
                                <option value="Bagbag II" {{ old('barangay') == 'Bagbag II' ? 'selected' : '' }}>Bagbag II</option>
                                <option value="Kanluran" {{ old('barangay') == 'Kanluran' ? 'selected' : '' }}>Kanluran</option>
                                <option value="Ligtong I" {{ old('barangay') == 'Ligtong I' ? 'selected' : '' }}>Ligtong I</option>
                                <option value="Ligtong II" {{ old('barangay') == 'Ligtong II' ? 'selected' : '' }}>Ligtong II</option>
                                <option value="Ligtong III" {{ old('barangay') == 'Ligtong III' ? 'selected' : '' }}>Ligtong III</option>
                                <option value="Ligtong IV" {{ old('barangay') == 'Ligtong IV' ? 'selected' : '' }}>Ligtong IV</option>
                                <option value="Muzon I" {{ old('barangay') == 'Muzon I' ? 'selected' : '' }}>Muzon I</option>
                                <option value="Muzon II" {{ old('barangay') == 'Muzon II' ? 'selected' : '' }}>Muzon II</option>
                                <option value="Poblacion" {{ old('barangay') == 'Poblacion' ? 'selected' : '' }}>Poblacion</option>
                                <option value="Sapa I" {{ old('barangay') == 'Sapa I' ? 'selected' : '' }}>Sapa I</option>
                                <option value="Sapa II" {{ old('barangay') == 'Sapa II' ? 'selected' : '' }}>Sapa II</option>
                                <option value="Sapa III" {{ old('barangay') == 'Sapa III' ? 'selected' : '' }}>Sapa III</option>
                                <option value="Sapa IV" {{ old('barangay') == 'Sapa IV' ? 'selected' : '' }}>Sapa IV</option>
                                <option value="Silangan I" {{ old('barangay') == 'Silangan I' ? 'selected' : '' }}>Silangan I</option>
                                <option value="Silangan II" {{ old('barangay') == 'Silangan II' ? 'selected' : '' }}>Silangan II</option>
                                <option value="Tejeros Convention" {{ old('barangay') == 'Tejeros Convention' ? 'selected' : '' }}>Tejeros Convention</option>
                                <option value="Wawa I" {{ old('barangay') == 'Wawa I' ? 'selected' : '' }}>Wawa I</option>
                                <option value="Wawa II" {{ old('barangay') == 'Wawa II' ? 'selected' : '' }}>Wawa II</option>
                                <option value="Wawa III" {{ old('barangay') == 'Wawa III' ? 'selected' : '' }}>Wawa III</option>
                            </select>
                            @error('barangay')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- Hidden field for complete address -->
                            <input type="hidden" id="address" name="address">

                            <!-- Address Preview -->
                            <div id="address_preview" class="mt-2 p-2 bg-light rounded border" style="display: none;">
                                <small class="text-muted">Complete Address:</small>
                                <div class="fw-semibold text-dark" id="preview_text"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="business_license_id" class="form-label fw-bold">Business License ID <span class="text-danger">*</span></label>
                            <input type="text" name="business_license_id" id="business_license_id"
                                   class="form-control @error('business_license_id') is-invalid @enderror"
                                   placeholder="Business license ID"
                                   value="{{ old('business_license_id') }}" 
                                   pattern="[0-9]+"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                   required>
                            <small class="text-muted">Enter the business license ID exactly as it appears on the business qualifications.</small>
                            @error('business_license_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <a href="{{ route('landing') }}" class="btn btn-outline-secondary px-4">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>

                            <button type="button" class="btn btn-primary px-4 next-step">
                                Next <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: Verify Business --}}
                    <div class="form-step" data-step="2">
                        <h5 class="mb-3 text-primary">Verify Your Business</h5>
                        <p class="text-muted mb-4">Upload clear photos of your business documents</p>

                        {{-- Business Permit --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-file-earmark-text me-1"></i>Business Permit <span class="text-danger">*</span>
                            </label>
                            <div class="upload-box" data-input="business_permit">
                                <input type="file" name="business_permit" id="business_permit" class="d-none" accept="image/jpeg,image/jpg,image/png" required>
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #088a50;"></i>
                                    <p class="mb-0 mt-2">Click to upload photo</p>
                                    <small class="text-muted">JPG, PNG (Max: 10MB)</small>
                                </div>
                                <div class="upload-preview" style="display: none;">
                                    <img src="" alt="Preview" class="preview-image">
                                    <div class="preview-info">
                                        <p class="file-name mb-1"></p>
                                        <button type="button" class="btn btn-sm btn-danger remove-file">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sanitation Certificate --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-file-earmark-medical me-1"></i>Sanitation Certificate <span class="text-danger">*</span>
                            </label>
                            <div class="upload-box" data-input="sanitation_cert">
                                <input type="file" name="sanitation_cert" id="sanitation_cert" class="d-none" accept="image/jpeg,image/jpg,image/png" required>
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #088a50;"></i>
                                    <p class="mb-0 mt-2">Click to upload photo</p>
                                    <small class="text-muted">JPG, PNG (Max: 10MB)</small>
                                </div>
                                <div class="upload-preview" style="display: none;">
                                    <img src="" alt="Preview" class="preview-image">
                                    <div class="preview-info">
                                        <p class="file-name mb-1"></p>
                                        <button type="button" class="btn btn-sm btn-danger remove-file">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Government ID 1 --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-person-badge me-1"></i>Government-Issued ID #1 <span class="text-danger">*</span>
                            </label>
                            <div class="upload-box" data-input="govt_id_1">
                                <input type="file" name="govt_id_1" id="govt_id_1" class="d-none" accept="image/jpeg,image/jpg,image/png" required>
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #088a50;"></i>
                                    <p class="mb-0 mt-2">Click to upload photo</p>
                                    <small class="text-muted">JPG, PNG (Max: 10MB)</small>
                                </div>
                                <div class="upload-preview" style="display: none;">
                                    <img src="" alt="Preview" class="preview-image">
                                    <div class="preview-info">
                                        <p class="file-name mb-1"></p>
                                        <button type="button" class="btn btn-sm btn-danger remove-file">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Government ID 2 --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-person-badge me-1"></i>Government-Issued ID #2 <span class="text-danger">*</span>
                            </label>
                            <div class="upload-box" data-input="govt_id_2">
                                <input type="file" name="govt_id_2" id="govt_id_2" class="d-none" accept="image/jpeg,image/jpg,image/png" required>
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-upload" style="font-size: 2rem; color: #088a50;"></i>
                                    <p class="mb-0 mt-2">Click to upload photo</p>
                                    <small class="text-muted">JPG, PNG (Max: 10MB)</small>
                                </div>
                                <div class="upload-preview" style="display: none;">
                                    <img src="" alt="Preview" class="preview-image">
                                    <div class="preview-info">
                                        <p class="file-name mb-1"></p>
                                        <button type="button" class="btn btn-sm btn-danger remove-file">
                                            <i class="bi bi-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Important Notes:</strong>
                            <ul class="mb-0 mt-2 small">
                                <li>Each photo must be less than 10 MB</li>
                                <li>Images must be clear, color, and high-resolution</li>
                                <li>Documents must be valid and not expired</li>
                                <li>Government IDs must show your full name and photo</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-4 prev-step">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                            <button type="button" class="btn btn-primary px-4 next-step">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: Review & Submit --}}
                    <div class="form-step" data-step="3">
                        <h5 class="mb-2 text-primary">Submit Registration</h5>
                        <p class="text-muted mb-3">
                            <i class="bi bi-info-circle me-1"></i>Please review your information before submitting.
                        </p>

                        <div class="review-section">
                            <h6 class="mb-2 fw-bold text-secondary">Registration Information</h6>
                            
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">Legal business name:</td>
                                        <td class="fw-bold" id="review_business_name"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Email address:</td>
                                        <td id="review_email"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Phone number:</td>
                                        <td id="review_phone"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Address:</td>
                                        <td id="review_address"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Business Permit Number:</td>
                                        <td id="review_license"></td>
                                    </tr>
                                </tbody>
                            </table>

                            <h6 class="mb-2 mt-3 fw-bold text-secondary">Uploaded Documents</h6>
                            <div class="row g-2">
                                <div class="col-12" id="review_business_permit"></div>
                                <div class="col-12" id="review_sanitation_cert"></div>
                                <div class="col-12" id="review_govt_id_1"></div>
                                <div class="col-12" id="review_govt_id_2"></div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3 mb-3" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> By submitting, you confirm all information is accurate.
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-4 prev-step">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                            <button type="submit" class="btn px-4 py-2 text-white" style="background: linear-gradient(135deg, #088a50 0%, #0bb364 100%);">
                                <i class="bi bi-check-circle me-2"></i>Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
    .progress-line {
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 2px;
        background-color: #e0e0e0;
        z-index: 0;
    }

    .step-item {
        position: relative;
        z-index: 1;
        flex: 1;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f0f0f0;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: bold;
        border: 2px solid #e0e0e0;
        transition: all 0.3s ease;
    }

    .step-circle.active {
        background: linear-gradient(135deg, #088a50 0%, #0bb364 100%);
        color: white;
        border-color: #088a50;
    }

    .step-circle.completed {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }

    .step-label {
        display: block;
        color: #999;
        font-size: 12px;
    }

    .step-item[data-step="1"] .step-circle.active ~ .step-label,
    .step-item[data-step="2"] .step-circle.active ~ .step-label,
    .step-item[data-step="3"] .step-circle.active ~ .step-label {
        color: #088a50;
        font-weight: 600;
    }

    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .upload-box {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #fafafa;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .upload-box:hover {
        border-color: #088a50;
        background-color: #f8f9ff;
    }

    .upload-preview {
        width: 100%;
    }

    .preview-image {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        margin-bottom: 10px;
        object-fit: cover;
    }

    .preview-info {
        text-align: center;
    }

    .file-name {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    .review-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e9ecef;
    }

    .review-section table tr {
        border-bottom: 1px solid #e9ecef;
    }

    .review-section table tr:last-child {
        border-bottom: none;
    }

    .review-section table td {
        padding: 10px 0;
    }

    .document-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        background-color: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 6px;
        font-size: 14px;
        color: #0066cc;
        margin-bottom: 8px;
    }

    .document-badge i {
        margin-right: 8px;
        font-size: 16px;
    }
body, 
h1, h2, h3, h4, h5, h6, 
p, span, a, div, input, select, button, label {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 3;
        const uploadedFiles = {
            business_permit: null,
            sanitation_cert: null,
            govt_id_1: null,
            govt_id_2: null
        };

        // Address Handling
        function initializeAddressHandling() {
            const streetInput = document.getElementById('street_address');
            const barangaySelect = document.getElementById('barangay');
            const addressInput = document.getElementById('address');
            const addressPreview = document.getElementById('address_preview');
            const previewText = document.getElementById('preview_text');

            function updateAddress() {
                const street = streetInput.value.trim();
                const barangay = barangaySelect.value;

                if (street && barangay) {
                    const completeAddress = `${street}, Barangay ${barangay}, Rosario, Cavite`;
                    addressInput.value = completeAddress;
                    previewText.textContent = completeAddress;
                    addressPreview.style.display = 'block';
                } else {
                    addressInput.value = '';
                    addressPreview.style.display = 'none';
                }
            }

            streetInput.addEventListener('input', updateAddress);
            barangaySelect.addEventListener('change', updateAddress);

            if (streetInput.value || barangaySelect.value) {
                updateAddress();
            }
        }

        // Phone Number Validation
        function initializePhoneValidation() {
            const phoneInput = document.getElementById('phone_number');

            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^\d+]/g, '');
                
                if (value.includes('+')) {
                    const plusCount = (value.match(/\+/g) || []).length;
                    if (plusCount > 1 || value.indexOf('+') !== 0) {
                        value = value.replace(/\+/g, '');
                    }
                }
                
                if (value.startsWith('+639')) {
                    value = value.substring(0, 13);
                } else if (value.startsWith('09')) {
                    value = value.substring(0, 11);
                }
                
                e.target.value = value;
            });

            phoneInput.addEventListener('blur', function(e) {
                const value = e.target.value;
                const pattern = /^(09|\+639)\d{9}$/;
                
                if (value && !pattern.test(value)) {
                    e.target.setCustomValidity('Please enter a valid Philippine mobile number');
                } else {
                    e.target.setCustomValidity('');
                }
            });
        }

        // File Upload Handling
        function initializeFileUploads() {
            const uploadBoxes = document.querySelectorAll('.upload-box');
            
            uploadBoxes.forEach(box => {
                const inputId = box.getAttribute('data-input');
                const fileInput = document.getElementById(inputId);
                const placeholder = box.querySelector('.upload-placeholder');
                const preview = box.querySelector('.upload-preview');
                const previewImage = preview.querySelector('.preview-image');
                const fileName = preview.querySelector('.file-name');
                const removeBtn = preview.querySelector('.remove-file');

                // Click to upload
                box.addEventListener('click', function(e) {
                    if (!e.target.closest('.remove-file')) {
                        fileInput.click();
                    }
                });

                // Drag and drop
                box.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    box.style.borderColor = '#088a50';
                    box.style.backgroundColor = '#f8f9ff';
                });

                box.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    box.style.borderColor = '';
                    box.style.backgroundColor = '';
                });

                box.addEventListener('drop', function(e) {
                    e.preventDefault();
                    box.style.borderColor = '';
                    box.style.backgroundColor = '';
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        const dt = new DataTransfer();
                        dt.items.add(files[0]);
                        fileInput.files = dt.files;
                        handleFileSelect(inputId, files[0]);
                    }
                });

                // File input change
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        handleFileSelect(inputId, e.target.files[0]);
                    }
                });

                // Remove file
                removeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    fileInput.value = '';
                    uploadedFiles[inputId] = null;
                    placeholder.style.display = 'block';
                    preview.style.display = 'none';
                    previewImage.src = '';
                });

                function handleFileSelect(id, file) {
                    // Validate file size (10MB)
                    if (file.size > 10 * 1024 * 1024) {
                        alert('File size must be less than 10MB');
                        fileInput.value = '';
                        return;
                    }

                    // Validate file type
                    if (!['image/jpeg', 'image/jpg', 'image/png'].includes(file.type)) {
                        alert('Only JPG and PNG files are allowed');
                        fileInput.value = '';
                        return;
                    }

                    uploadedFiles[id] = file;

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        fileName.textContent = file.name;
                        placeholder.style.display = 'none';
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Navigation
        document.querySelectorAll('.next-step').forEach(btn => {
            btn.addEventListener('click', function() {
                if (validateStep(currentStep)) {
                    if (currentStep < totalSteps) {
                        goToStep(currentStep + 1);
                    }
                }
            });
        });

        document.querySelectorAll('.prev-step').forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            });
        });

        function goToStep(step) {
            document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');
            document.querySelector(`.step-item[data-step="${currentStep}"] .step-circle`).classList.remove('active');
            
            if (step > currentStep) {
                document.querySelector(`.step-item[data-step="${currentStep}"] .step-circle`).classList.add('completed');
            } else if (step < currentStep) {
                document.querySelector(`.step-item[data-step="${currentStep}"] .step-circle`).classList.remove('completed');
            }

            currentStep = step;

            document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('active');
            document.querySelector(`.step-item[data-step="${currentStep}"] .step-circle`).classList.add('active');

            if (currentStep === 3) {
                populateReview();
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep(step) {
            const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
            let valid = true;
            let firstInvalid = null;

            if (step === 1) {
                const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
                inputs.forEach(input => {
                    // Skip hidden address field
                    if (input.id === 'address') return;
                    
                    if (!input.value.trim()) {
                        input.classList.add('is-invalid');
                        if (!firstInvalid) firstInvalid = input;
                        valid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
            } else if (step === 2) {
                const requiredFiles = ['business_permit', 'sanitation_cert', 'govt_id_1', 'govt_id_2'];
                const missingFiles = [];
                
                requiredFiles.forEach(fileId => {
                    const fileInput = document.getElementById(fileId);
                    const uploadBox = document.querySelector(`.upload-box[data-input="${fileId}"]`);
                    
                    if (!fileInput.files.length) {
                        uploadBox.style.borderColor = '#dc3545';
                        if (!firstInvalid) firstInvalid = uploadBox;
                        missingFiles.push(fileId.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
                        valid = false;
                    } else {
                        uploadBox.style.borderColor = '';
                    }
                });

                if (!valid) {
                    alert('Please upload the following documents:\n- ' + missingFiles.join('\n- '));
                }
            }

            if (!valid && firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            return valid;
        }

        function populateReview() {
            // Populate basic info
            document.getElementById('review_business_name').textContent = document.getElementById('business_name').value || 'N/A';
            document.getElementById('review_email').textContent = document.getElementById('email_address').value || 'N/A';
            
            const phone = document.getElementById('phone_number').value;
            document.getElementById('review_phone').textContent = phone || 'Not provided';
            
            const address = document.getElementById('address').value;
            document.getElementById('review_address').textContent = address || 'Not provided';
            
            document.getElementById('review_license').textContent = document.getElementById('business_license_id').value || 'N/A';
            
            // Display uploaded documents
            const docLabels = {
                business_permit: 'Business Permit',
                sanitation_cert: 'Sanitation Certificate',
                govt_id_1: 'Government ID #1',
                govt_id_2: 'Government ID #2'
            };

            Object.keys(docLabels).forEach(key => {
                const reviewEl = document.getElementById(`review_${key}`);
                const fileInput = document.getElementById(key);
                
                if (fileInput && fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    reviewEl.innerHTML = `
                        <div class="document-badge">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>${docLabels[key]}: ${file.name}</span>
                        </div>
                    `;
                } else {
                    reviewEl.innerHTML = `
                        <div class="text-danger small">
                            <i class="bi bi-x-circle-fill"></i> ${docLabels[key]}: Not uploaded
                        </div>
                    `;
                }
            });

            console.log('Review populated successfully');
        }

        // Form submission validation
        document.getElementById('resellerForm').addEventListener('submit', function(e) {
            const requiredFiles = ['business_permit', 'sanitation_cert', 'govt_id_1', 'govt_id_2'];
            const missingFiles = [];

            requiredFiles.forEach(fileId => {
                const fileInput = document.getElementById(fileId);
                if (!fileInput.files.length) {
                    missingFiles.push(fileId.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
                }
            });

            if (missingFiles.length > 0) {
                e.preventDefault();
                alert('Missing documents:\n- ' + missingFiles.join('\n- '));
                goToStep(2);
                return false;
            }

            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Submitting...';
        });

        // Initialize all functions
        initializeAddressHandling();
        initializePhoneValidation();
        initializeFileUploads();
    });
</script>
@endsection