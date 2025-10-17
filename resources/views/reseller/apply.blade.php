@extends('layouts.app')

@section('title', 'Apply as Reseller')

<div class="container mt-5 mb-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header text-white text-center py-4 rounded-top-4"
             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3 class="mb-0">Business Registration</h3>
            <p class="mb-0 mt-2 small">Apply to become an authorized Supplier</p>
        </div>

        <div class="card-body p-5">

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
                <div class="text-center py-5">
                    @if($application->status == 'pending')
                        <div class="mb-4">
                            <i class="bi bi-clock-history text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="mb-3">Application Under Review</h4>
                        <p class="text-muted mb-4">Your reseller application is currently being reviewed by our team.</p>
                        <div class="alert alert-info d-inline-block">
                            <strong>Status:</strong> <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                    @elseif($application->status == 'approved')
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="mb-3">Congratulations!</h4>
                        <p class="text-muted mb-4">Your reseller application has been approved. You can now access reseller features.</p>
                        <div class="alert alert-success d-inline-block">
                            <strong>Status:</strong> <span class="badge bg-success">Approved</span>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary px-4">Go to Dashboard</a>
                        </div>
                    @elseif($application->status == 'rejected')
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="mb-3">Application Rejected</h4>
                        <p class="text-muted mb-4">Unfortunately, your reseller application has been rejected.</p>
                        @if($application->rejection_reason)
                            <div class="alert alert-danger">
                                <strong>Reason:</strong> {{ $application->rejection_reason }}
                            </div>
                        @endif
                        <p class="text-muted small">Please contact our support team for more details.</p>
                        <div class="mt-4">
                            <a href="mailto:support@yourstore.com" class="btn btn-outline-primary px-4">Contact Support</a>
                        </div>
                    @endif
                </div>
            @else
                {{-- ✅ Multi-Step Progress Indicator --}}
                <div class="mb-5">
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
                        <h5 class="mb-4 text-primary">Basic Information</h5>

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
                            <label for="country" class="form-label fw-bold">Country or Region <span class="text-danger">*</span></label>
                            <select name="country" id="country" class="form-select @error('country') is-invalid @enderror" required>
                                <option value="">Select</option>
                                <option value="Philippines" {{ old('country') == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                                <option value="USA" {{ old('country') == 'USA' ? 'selected' : '' }}>United States</option>
                                <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                                <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                            </select>
                            <small class="text-muted">Select the country or region exactly as it appears on the business qualifications.</small>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" id="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   placeholder="Business address"
                                   value="{{ old('address') }}" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="province" class="form-label fw-bold">Province or State <span class="text-danger">*</span></label>
                                <select name="province" id="province" class="form-select @error('province') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    <option value="Metro Manila" {{ old('province') == 'Metro Manila' ? 'selected' : '' }}>Metro Manila</option>
                                    <option value="Cavite" {{ old('province') == 'Cavite' ? 'selected' : '' }}>Cavite</option>
                                    <option value="Laguna" {{ old('province') == 'Laguna' ? 'selected' : '' }}>Laguna</option>
                                    <option value="Rizal" {{ old('province') == 'Rizal' ? 'selected' : '' }}>Rizal</option>
                                    <option value="Bulacan" {{ old('province') == 'Bulacan' ? 'selected' : '' }}>Bulacan</option>
                                </select>
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label fw-bold">City <span class="text-danger">*</span></label>
                                <select name="city" id="city" class="form-select @error('city') is-invalid @enderror" required>
                                    <option value="">Select</option>
                                    <option value="Makati" {{ old('city') == 'Makati' ? 'selected' : '' }}>Makati</option>
                                    <option value="Manila" {{ old('city') == 'Manila' ? 'selected' : '' }}>Manila</option>
                                    <option value="Quezon City" {{ old('city') == 'Quezon City' ? 'selected' : '' }}>Quezon City</option>
                                    <option value="Pasig" {{ old('city') == 'Pasig' ? 'selected' : '' }}>Pasig</option>
                                    <option value="Taguig" {{ old('city') == 'Taguig' ? 'selected' : '' }}>Taguig</option>
                                </select>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="zip_code" class="form-label fw-bold">Zip or Postal Code <span class="text-danger">*</span></label>
                                <input type="text" name="zip_code" id="zip_code"
                                       class="form-control @error('zip_code') is-invalid @enderror"
                                       placeholder="Zip or postal code"
                                       value="{{ old('zip_code') }}" required>
                                @error('zip_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" id="phone_number"
                                       class="form-control @error('phone_number') is-invalid @enderror"
                                       placeholder="Business phone number (e.g., +63 912 345 6789)"
                                       value="{{ old('phone_number') }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="business_license_id" class="form-label fw-bold">Business License ID <span class="text-danger">*</span></label>
                            <input type="text" name="business_license_id" id="business_license_id"
                                   class="form-control @error('business_license_id') is-invalid @enderror"
                                   placeholder="Business license ID"
                                   value="{{ old('business_license_id') }}" required>
                            <small class="text-muted">Enter the business license ID exactly as it appears on the business qualifications.</small>
                            @error('business_license_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary px-5 next-step">Next</button>
                        </div>
                    </div>

                    {{-- STEP 2: Verify Business --}}
                    <div class="form-step" data-step="2">
                        <h5 class="mb-4 text-primary">Verify Your Business</h5>

                        <div class="mb-4">
                            <p class="fw-bold mb-3">Upload Business Qualification Documents</p>
                            
                            <div class="upload-area border border-2 border-dashed rounded p-5 text-center" id="uploadArea">
                                <div class="upload-icon mb-3">
                                    <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #667eea;"></i>
                                </div>
                                <input type="file" name="pdf_file" id="pdf_file" class="d-none" accept=".pdf,.jpg,.jpeg,.png" required>
                                <p class="mb-2 text-muted">Click to upload or drag and drop</p>
                                <small class="text-muted">Supported formats: PDF, JPG, PNG (Max: 10MB)</small>
                                <div id="fileList" class="mt-3"></div>
                            </div>

                            <div class="mt-3">
                                <p class="mb-2 small fw-bold">Notes:</p>
                                <ul class="small text-muted">
                                    <li>Each file must be less than 10 MB.</li>
                                    <li>Images must be color, hi-res color, and contain the business legal name and required information.</li>
                                    <li>Documents must be valid and can't be expired or modified.</li>
                                    <li>Files must be JPG, PNG, or PDF format.</li>
                                    <li>Include all relevant business permits, licenses, and registration documents.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary px-5 prev-step">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                            <button type="button" class="btn btn-primary px-5 next-step">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: Review & Submit --}}
                    <div class="form-step" data-step="3">
                        <h5 class="mb-3 text-primary">Submit Registration</h5>
                        <p class="text-muted mb-4">
                            <i class="bi bi-info-circle me-2"></i>Please review your information before submitting. You can only change registered business information every 5 months.
                        </p>

                        <div class="review-section">
                            <h6 class="mb-3 fw-bold text-secondary">Registration Information</h6>
                            
                            <table class="table table-borderless">
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
                                        <td class="text-muted">Country or region:</td>
                                        <td id="review_country"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Address:</td>
                                        <td id="review_address"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">City:</td>
                                        <td id="review_city"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Province or state:</td>
                                        <td id="review_province"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Zip or postal code:</td>
                                        <td id="review_zip"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Phone number:</td>
                                        <td id="review_phone"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Business license ID:</td>
                                        <td id="review_license"></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Uploaded document:</td>
                                        <td id="review_file"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-warning mt-4" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> By submitting this application, you confirm that all information provided is accurate and truthful.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary px-5 prev-step">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                            <button type="submit" class="btn px-5 py-2 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
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
        color: #667eea;
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

    .upload-area {
        cursor: pointer;
        transition: all 0.3s ease;
        background-color: #fafafa;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .upload-area:hover {
        border-color: #667eea !important;
        background-color: #f8f9ff;
    }

    .upload-icon {
        transition: transform 0.3s ease;
    }

    .upload-area:hover .upload-icon {
        transform: scale(1.1);
    }

    .review-section {
        background-color: #f8f9fa;
        padding: 30px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .review-section table tr {
        border-bottom: 1px solid #e9ecef;
    }

    .review-section table tr:last-child {
        border-bottom: none;
    }

    .review-section table td {
        padding: 12px 0;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 1;
        const totalSteps = 3;
        let uploadedFile = null;

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
            const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
            let valid = true;
            let firstInvalid = null;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    if (!firstInvalid) firstInvalid = input;
                    valid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!valid && firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            return valid;
        }

        function populateReview() {
            document.getElementById('review_business_name').textContent = document.getElementById('business_name').value;
            document.getElementById('review_email').textContent = document.getElementById('email_address').value;
            document.getElementById('review_country').textContent = document.getElementById('country').value;
            document.getElementById('review_address').textContent = document.getElementById('address').value;
            document.getElementById('review_city').textContent = document.getElementById('city').value;
            document.getElementById('review_province').textContent = document.getElementById('province').value;
            document.getElementById('review_zip').textContent = document.getElementById('zip_code').value;
            
            const phone = document.getElementById('phone_number').value;
            document.getElementById('review_phone').textContent = phone ? phone : 'Not provided';
            
            document.getElementById('review_license').textContent = document.getElementById('business_license_id').value;
            
            const fileName = uploadedFile ? uploadedFile.name : 'No file uploaded';
            document.getElementById('review_file').textContent = fileName;
        }

        // File upload handling
        const uploadArea = document.getElementById('uploadArea');
        const fileList = document.getElementById('fileList');
        const pdfFileInput = document.getElementById('pdf_file');
        
        uploadArea.addEventListener('click', function(e) {
            if (e.target !== pdfFileInput) {
                pdfFileInput.click();
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#667eea';
            uploadArea.style.backgroundColor = '#f8f9ff';
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '';
            uploadArea.style.backgroundColor = '';
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '';
            uploadArea.style.backgroundColor = '';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                pdfFileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        pdfFileInput.addEventListener('change', function(e) {
            fileList.innerHTML = '';
            if (e.target.files.length > 0) {
                handleFileSelect(e.target.files[0]);
            }
        });

        function handleFileSelect(file) {
            uploadedFile = file;
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileExt = fileName.split('.').pop().toUpperCase();
            
            const fileItem = document.createElement('div');
            fileItem.className = 'alert alert-success small py-2 px-3 mb-0 d-flex align-items-center justify-content-between';
            fileItem.innerHTML = `
                <div>
                    <i class="bi bi-file-earmark-${fileExt === 'PDF' ? 'pdf' : 'image'}-fill me-2"></i>
                    <strong>${fileName}</strong> (${fileSize} MB)
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="clearFile()">
                    <i class="bi bi-x-circle"></i>
                </button>
            `;
            fileList.appendChild(fileItem);
        }

        window.clearFile = function() {
            pdfFileInput.value = '';
            uploadedFile = null;
            fileList.innerHTML = '';
        };

        // Form submission
        document.getElementById('resellerForm').addEventListener('submit', function(e) {
            if (!uploadedFile) {
                e.preventDefault();
                alert('Please upload a business qualification document.');
                goToStep(2);
            }
        });
    });
</script>
