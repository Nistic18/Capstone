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
                            <label for="address" class="form-label fw-bold">Address & Barangay <span class="text-danger">*</span></label>
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
            <option value="">Select Province</option>
            
            <!-- NCR - National Capital Region -->
            <optgroup label="NCR - National Capital Region">
                <option value="Metro Manila" {{ old('province') == 'Metro Manila' ? 'selected' : '' }}>Metro Manila</option>
            </optgroup>
            
            <!-- CAR - Cordillera Administrative Region -->
            <optgroup label="CAR - Cordillera Administrative Region">
                <option value="Abra" {{ old('province') == 'Abra' ? 'selected' : '' }}>Abra</option>
                <option value="Apayao" {{ old('province') == 'Apayao' ? 'selected' : '' }}>Apayao</option>
                <option value="Benguet" {{ old('province') == 'Benguet' ? 'selected' : '' }}>Benguet</option>
                <option value="Ifugao" {{ old('province') == 'Ifugao' ? 'selected' : '' }}>Ifugao</option>
                <option value="Kalinga" {{ old('province') == 'Kalinga' ? 'selected' : '' }}>Kalinga</option>
                <option value="Mountain Province" {{ old('province') == 'Mountain Province' ? 'selected' : '' }}>Mountain Province</option>
            </optgroup>
            
            <!-- Region I - Ilocos Region -->
            <optgroup label="Region I - Ilocos Region">
                <option value="Ilocos Norte" {{ old('province') == 'Ilocos Norte' ? 'selected' : '' }}>Ilocos Norte</option>
                <option value="Ilocos Sur" {{ old('province') == 'Ilocos Sur' ? 'selected' : '' }}>Ilocos Sur</option>
                <option value="La Union" {{ old('province') == 'La Union' ? 'selected' : '' }}>La Union</option>
                <option value="Pangasinan" {{ old('province') == 'Pangasinan' ? 'selected' : '' }}>Pangasinan</option>
            </optgroup>
            
            <!-- Region II - Cagayan Valley -->
            <optgroup label="Region II - Cagayan Valley">
                <option value="Batanes" {{ old('province') == 'Batanes' ? 'selected' : '' }}>Batanes</option>
                <option value="Cagayan" {{ old('province') == 'Cagayan' ? 'selected' : '' }}>Cagayan</option>
                <option value="Isabela" {{ old('province') == 'Isabela' ? 'selected' : '' }}>Isabela</option>
                <option value="Nueva Vizcaya" {{ old('province') == 'Nueva Vizcaya' ? 'selected' : '' }}>Nueva Vizcaya</option>
                <option value="Quirino" {{ old('province') == 'Quirino' ? 'selected' : '' }}>Quirino</option>
            </optgroup>
            
            <!-- Region III - Central Luzon -->
            <optgroup label="Region III - Central Luzon">
                <option value="Aurora" {{ old('province') == 'Aurora' ? 'selected' : '' }}>Aurora</option>
                <option value="Bataan" {{ old('province') == 'Bataan' ? 'selected' : '' }}>Bataan</option>
                <option value="Bulacan" {{ old('province') == 'Bulacan' ? 'selected' : '' }}>Bulacan</option>
                <option value="Nueva Ecija" {{ old('province') == 'Nueva Ecija' ? 'selected' : '' }}>Nueva Ecija</option>
                <option value="Pampanga" {{ old('province') == 'Pampanga' ? 'selected' : '' }}>Pampanga</option>
                <option value="Tarlac" {{ old('province') == 'Tarlac' ? 'selected' : '' }}>Tarlac</option>
                <option value="Zambales" {{ old('province') == 'Zambales' ? 'selected' : '' }}>Zambales</option>
            </optgroup>
            
            <!-- Region IV-A - CALABARZON -->
            <optgroup label="Region IV-A - CALABARZON">
                <option value="Batangas" {{ old('province') == 'Batangas' ? 'selected' : '' }}>Batangas</option>
                <option value="Cavite" {{ old('province') == 'Cavite' ? 'selected' : '' }}>Cavite</option>
                <option value="Laguna" {{ old('province') == 'Laguna' ? 'selected' : '' }}>Laguna</option>
                <option value="Quezon" {{ old('province') == 'Quezon' ? 'selected' : '' }}>Quezon</option>
                <option value="Rizal" {{ old('province') == 'Rizal' ? 'selected' : '' }}>Rizal</option>
            </optgroup>
            
            <!-- Region IV-B - MIMAROPA -->
            <optgroup label="Region IV-B - MIMAROPA">
                <option value="Marinduque" {{ old('province') == 'Marinduque' ? 'selected' : '' }}>Marinduque</option>
                <option value="Occidental Mindoro" {{ old('province') == 'Occidental Mindoro' ? 'selected' : '' }}>Occidental Mindoro</option>
                <option value="Oriental Mindoro" {{ old('province') == 'Oriental Mindoro' ? 'selected' : '' }}>Oriental Mindoro</option>
                <option value="Palawan" {{ old('province') == 'Palawan' ? 'selected' : '' }}>Palawan</option>
                <option value="Romblon" {{ old('province') == 'Romblon' ? 'selected' : '' }}>Romblon</option>
            </optgroup>
            
            <!-- Region V - Bicol Region -->
            <optgroup label="Region V - Bicol Region">
                <option value="Albay" {{ old('province') == 'Albay' ? 'selected' : '' }}>Albay</option>
                <option value="Camarines Norte" {{ old('province') == 'Camarines Norte' ? 'selected' : '' }}>Camarines Norte</option>
                <option value="Camarines Sur" {{ old('province') == 'Camarines Sur' ? 'selected' : '' }}>Camarines Sur</option>
                <option value="Catanduanes" {{ old('province') == 'Catanduanes' ? 'selected' : '' }}>Catanduanes</option>
                <option value="Masbate" {{ old('province') == 'Masbate' ? 'selected' : '' }}>Masbate</option>
                <option value="Sorsogon" {{ old('province') == 'Sorsogon' ? 'selected' : '' }}>Sorsogon</option>
            </optgroup>
            
            <!-- Region VI - Western Visayas -->
            <optgroup label="Region VI - Western Visayas">
                <option value="Aklan" {{ old('province') == 'Aklan' ? 'selected' : '' }}>Aklan</option>
                <option value="Antique" {{ old('province') == 'Antique' ? 'selected' : '' }}>Antique</option>
                <option value="Capiz" {{ old('province') == 'Capiz' ? 'selected' : '' }}>Capiz</option>
                <option value="Guimaras" {{ old('province') == 'Guimaras' ? 'selected' : '' }}>Guimaras</option>
                <option value="Iloilo" {{ old('province') == 'Iloilo' ? 'selected' : '' }}>Iloilo</option>
                <option value="Negros Occidental" {{ old('province') == 'Negros Occidental' ? 'selected' : '' }}>Negros Occidental</option>
            </optgroup>
            
            <!-- Region VII - Central Visayas -->
            <optgroup label="Region VII - Central Visayas">
                <option value="Bohol" {{ old('province') == 'Bohol' ? 'selected' : '' }}>Bohol</option>
                <option value="Cebu" {{ old('province') == 'Cebu' ? 'selected' : '' }}>Cebu</option>
                <option value="Negros Oriental" {{ old('province') == 'Negros Oriental' ? 'selected' : '' }}>Negros Oriental</option>
                <option value="Siquijor" {{ old('province') == 'Siquijor' ? 'selected' : '' }}>Siquijor</option>
            </optgroup>
            
            <!-- Region VIII - Eastern Visayas -->
            <optgroup label="Region VIII - Eastern Visayas">
                <option value="Biliran" {{ old('province') == 'Biliran' ? 'selected' : '' }}>Biliran</option>
                <option value="Eastern Samar" {{ old('province') == 'Eastern Samar' ? 'selected' : '' }}>Eastern Samar</option>
                <option value="Leyte" {{ old('province') == 'Leyte' ? 'selected' : '' }}>Leyte</option>
                <option value="Northern Samar" {{ old('province') == 'Northern Samar' ? 'selected' : '' }}>Northern Samar</option>
                <option value="Samar" {{ old('province') == 'Samar' ? 'selected' : '' }}>Samar</option>
                <option value="Southern Leyte" {{ old('province') == 'Southern Leyte' ? 'selected' : '' }}>Southern Leyte</option>
            </optgroup>
            
            <!-- Region IX - Zamboanga Peninsula -->
            <optgroup label="Region IX - Zamboanga Peninsula">
                <option value="Zamboanga del Norte" {{ old('province') == 'Zamboanga del Norte' ? 'selected' : '' }}>Zamboanga del Norte</option>
                <option value="Zamboanga del Sur" {{ old('province') == 'Zamboanga del Sur' ? 'selected' : '' }}>Zamboanga del Sur</option>
                <option value="Zamboanga Sibugay" {{ old('province') == 'Zamboanga Sibugay' ? 'selected' : '' }}>Zamboanga Sibugay</option>
            </optgroup>
            
            <!-- Region X - Northern Mindanao -->
            <optgroup label="Region X - Northern Mindanao">
                <option value="Bukidnon" {{ old('province') == 'Bukidnon' ? 'selected' : '' }}>Bukidnon</option>
                <option value="Camiguin" {{ old('province') == 'Camiguin' ? 'selected' : '' }}>Camiguin</option>
                <option value="Lanao del Norte" {{ old('province') == 'Lanao del Norte' ? 'selected' : '' }}>Lanao del Norte</option>
                <option value="Misamis Occidental" {{ old('province') == 'Misamis Occidental' ? 'selected' : '' }}>Misamis Occidental</option>
                <option value="Misamis Oriental" {{ old('province') == 'Misamis Oriental' ? 'selected' : '' }}>Misamis Oriental</option>
            </optgroup>
            
            <!-- Region XI - Davao Region -->
            <optgroup label="Region XI - Davao Region">
                <option value="Davao de Oro" {{ old('province') == 'Davao de Oro' ? 'selected' : '' }}>Davao de Oro</option>
                <option value="Davao del Norte" {{ old('province') == 'Davao del Norte' ? 'selected' : '' }}>Davao del Norte</option>
                <option value="Davao del Sur" {{ old('province') == 'Davao del Sur' ? 'selected' : '' }}>Davao del Sur</option>
                <option value="Davao Occidental" {{ old('province') == 'Davao Occidental' ? 'selected' : '' }}>Davao Occidental</option>
                <option value="Davao Oriental" {{ old('province') == 'Davao Oriental' ? 'selected' : '' }}>Davao Oriental</option>
            </optgroup>
            
            <!-- Region XII - SOCCSKSARGEN -->
            <optgroup label="Region XII - SOCCSKSARGEN">
                <option value="Cotabato" {{ old('province') == 'Cotabato' ? 'selected' : '' }}>Cotabato</option>
                <option value="Sarangani" {{ old('province') == 'Sarangani' ? 'selected' : '' }}>Sarangani</option>
                <option value="South Cotabato" {{ old('province') == 'South Cotabato' ? 'selected' : '' }}>South Cotabato</option>
                <option value="Sultan Kudarat" {{ old('province') == 'Sultan Kudarat' ? 'selected' : '' }}>Sultan Kudarat</option>
            </optgroup>
            
            <!-- Region XIII - Caraga -->
            <optgroup label="Region XIII - Caraga">
                <option value="Agusan del Norte" {{ old('province') == 'Agusan del Norte' ? 'selected' : '' }}>Agusan del Norte</option>
                <option value="Agusan del Sur" {{ old('province') == 'Agusan del Sur' ? 'selected' : '' }}>Agusan del Sur</option>
                <option value="Dinagat Islands" {{ old('province') == 'Dinagat Islands' ? 'selected' : '' }}>Dinagat Islands</option>
                <option value="Surigao del Norte" {{ old('province') == 'Surigao del Norte' ? 'selected' : '' }}>Surigao del Norte</option>
                <option value="Surigao del Sur" {{ old('province') == 'Surigao del Sur' ? 'selected' : '' }}>Surigao del Sur</option>
            </optgroup>
            
            <!-- BARMM - Bangsamoro Autonomous Region in Muslim Mindanao -->
            <optgroup label="BARMM - Bangsamoro">
                <option value="Basilan" {{ old('province') == 'Basilan' ? 'selected' : '' }}>Basilan</option>
                <option value="Lanao del Sur" {{ old('province') == 'Lanao del Sur' ? 'selected' : '' }}>Lanao del Sur</option>
                <option value="Maguindanao del Norte" {{ old('province') == 'Maguindanao del Norte' ? 'selected' : '' }}>Maguindanao del Norte</option>
                <option value="Maguindanao del Sur" {{ old('province') == 'Maguindanao del Sur' ? 'selected' : '' }}>Maguindanao del Sur</option>
                <option value="Sulu" {{ old('province') == 'Sulu' ? 'selected' : '' }}>Sulu</option>
                <option value="Tawi-Tawi" {{ old('province') == 'Tawi-Tawi' ? 'selected' : '' }}>Tawi-Tawi</option>
            </optgroup>
        </select>
        @error('province')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="city" class="form-label fw-bold">City <span class="text-danger">*</span></label>
        <select name="city" id="city" class="form-select @error('city') is-invalid @enderror" required>
            <option value="">Select City/Municipality</option>
            
            <!-- Metro Manila -->
            <option value="Caloocan" data-province="Metro Manila">Caloocan</option>
            <option value="Las Piñas" data-province="Metro Manila">Las Piñas</option>
            <option value="Makati" data-province="Metro Manila">Makati</option>
            <option value="Malabon" data-province="Metro Manila">Malabon</option>
            <option value="Mandaluyong" data-province="Metro Manila">Mandaluyong</option>
            <option value="Manila" data-province="Metro Manila">Manila</option>
            <option value="Marikina" data-province="Metro Manila">Marikina</option>
            <option value="Muntinlupa" data-province="Metro Manila">Muntinlupa</option>
            <option value="Navotas" data-province="Metro Manila">Navotas</option>
            <option value="Parañaque" data-province="Metro Manila">Parañaque</option>
            <option value="Pasay" data-province="Metro Manila">Pasay</option>
            <option value="Pasig" data-province="Metro Manila">Pasig</option>
            <option value="Pateros" data-province="Metro Manila">Pateros</option>
            <option value="Quezon City" data-province="Metro Manila">Quezon City</option>
            <option value="San Juan" data-province="Metro Manila">San Juan</option>
            <option value="Taguig" data-province="Metro Manila">Taguig</option>
            <option value="Valenzuela" data-province="Metro Manila">Valenzuela</option>
            
            <!-- Cavite -->
            <option value="Bacoor" data-province="Cavite">Bacoor</option>
            <option value="Cavite City" data-province="Cavite">Cavite City</option>
            <option value="Dasmariñas" data-province="Cavite">Dasmariñas</option>
            <option value="General Trias" data-province="Cavite">General Trias</option>
            <option value="Imus" data-province="Cavite">Imus</option>
            <option value="Tagaytay" data-province="Cavite">Tagaytay</option>
            <option value="Trece Martires" data-province="Cavite">Trece Martires</option>
            
            <!-- Laguna -->
            <option value="Biñan" data-province="Laguna">Biñan</option>
            <option value="Cabuyao" data-province="Laguna">Cabuyao</option>
            <option value="Calamba" data-province="Laguna">Calamba</option>
            <option value="San Pablo" data-province="Laguna">San Pablo</option>
            <option value="San Pedro" data-province="Laguna">San Pedro</option>
            <option value="Santa Rosa" data-province="Laguna">Santa Rosa</option>
            
            <!-- Batangas -->
            <option value="Batangas City" data-province="Batangas">Batangas City</option>
            <option value="Lipa" data-province="Batangas">Lipa</option>
            <option value="Tanauan" data-province="Batangas">Tanauan</option>
            
            <!-- Quezon -->
            <option value="Lucena" data-province="Quezon">Lucena</option>
            <option value="Tayabas" data-province="Quezon">Tayabas</option>
            
            <!-- Rizal -->
            <option value="Antipolo" data-province="Rizal">Antipolo</option>
            <option value="Cainta" data-province="Rizal">Cainta</option>
            <option value="Taytay" data-province="Rizal">Taytay</option>
            
            <!-- Bulacan -->
            <option value="Malolos" data-province="Bulacan">Malolos</option>
            <option value="Meycauayan" data-province="Bulacan">Meycauayan</option>
            <option value="San Jose del Monte" data-province="Bulacan">San Jose del Monte</option>
            
            <!-- Pampanga -->
            <option value="Angeles" data-province="Pampanga">Angeles</option>
            <option value="Mabalacat" data-province="Pampanga">Mabalacat</option>
            <option value="San Fernando (Pampanga)" data-province="Pampanga">San Fernando</option>
            
            <!-- Nueva Ecija -->
            <option value="Cabanatuan" data-province="Nueva Ecija">Cabanatuan</option>
            <option value="Gapan" data-province="Nueva Ecija">Gapan</option>
            <option value="Muñoz" data-province="Nueva Ecija">Muñoz</option>
            <option value="Palayan" data-province="Nueva Ecija">Palayan</option>
            <option value="San Jose" data-province="Nueva Ecija">San Jose</option>
            
            <!-- Tarlac -->
            <option value="Tarlac City" data-province="Tarlac">Tarlac City</option>
            
            <!-- Zambales -->
            <option value="Olongapo" data-province="Zambales">Olongapo</option>
            
            <!-- Bataan -->
            <option value="Balanga" data-province="Bataan">Balanga</option>
            
            <!-- Benguet -->
            <option value="Baguio" data-province="Benguet">Baguio</option>
            <option value="La Trinidad" data-province="Benguet">La Trinidad</option>
            
            <!-- Ilocos Norte -->
            <option value="Batac" data-province="Ilocos Norte">Batac</option>
            <option value="Laoag" data-province="Ilocos Norte">Laoag</option>
            
            <!-- Ilocos Sur -->
            <option value="Candon" data-province="Ilocos Sur">Candon</option>
            <option value="Vigan" data-province="Ilocos Sur">Vigan</option>
            
            <!-- La Union -->
            <option value="San Fernando (La Union)" data-province="La Union">San Fernando</option>
            
            <!-- Pangasinan -->
            <option value="Alaminos" data-province="Pangasinan">Alaminos</option>
            <option value="Dagupan" data-province="Pangasinan">Dagupan</option>
            <option value="San Carlos" data-province="Pangasinan">San Carlos</option>
            <option value="Urdaneta" data-province="Pangasinan">Urdaneta</option>
            
            <!-- Cagayan -->
            <option value="Tuguegarao" data-province="Cagayan">Tuguegarao</option>
            
            <!-- Isabela -->
            <option value="Cauayan" data-province="Isabela">Cauayan</option>
            <option value="Ilagan" data-province="Isabela">Ilagan</option>
            <option value="Santiago" data-province="Isabela">Santiago</option>
            
            <!-- Albay -->
            <option value="Legazpi" data-province="Albay">Legazpi</option>
            <option value="Ligao" data-province="Albay">Ligao</option>
            <option value="Tabaco" data-province="Albay">Tabaco</option>
            
            <!-- Camarines Sur -->
            <option value="Iriga" data-province="Camarines Sur">Iriga</option>
            <option value="Naga" data-province="Camarines Sur">Naga</option>
            
            <!-- Sorsogon -->
            <option value="Sorsogon City" data-province="Sorsogon">Sorsogon City</option>
            
            <!-- Masbate -->
            <option value="Masbate City" data-province="Masbate">Masbate City</option>
            
            <!-- Palawan -->
            <option value="Puerto Princesa" data-province="Palawan">Puerto Princesa</option>
            
            <!-- Oriental Mindoro -->
            <option value="Calapan" data-province="Oriental Mindoro">Calapan</option>
            
            <!-- Iloilo -->
            <option value="Iloilo City" data-province="Iloilo">Iloilo City</option>
            <option value="Passi" data-province="Iloilo">Passi</option>
            
            <!-- Capiz -->
            <option value="Roxas" data-province="Capiz">Roxas</option>
            
            <!-- Negros Occidental -->
            <option value="Bacolod" data-province="Negros Occidental">Bacolod</option>
            <option value="Bago" data-province="Negros Occidental">Bago</option>
            <option value="Cadiz" data-province="Negros Occidental">Cadiz</option>
            <option value="Escalante" data-province="Negros Occidental">Escalante</option>
            <option value="Himamaylan" data-province="Negros Occidental">Himamaylan</option>
            <option value="Kabankalan" data-province="Negros Occidental">Kabankalan</option>
            <option value="Sagay" data-province="Negros Occidental">Sagay</option>
            <option value="San Carlos (Negros Occidental)" data-province="Negros Occidental">San Carlos</option>
            <option value="Silay" data-province="Negros Occidental">Silay</option>
            <option value="Sipalay" data-province="Negros Occidental">Sipalay</option>
            <option value="Talisay (Negros Occidental)" data-province="Negros Occidental">Talisay</option>
            <option value="Victorias" data-province="Negros Occidental">Victorias</option>
            
            <!-- Cebu -->
            <option value="Bogo" data-province="Cebu">Bogo</option>
            <option value="Carcar" data-province="Cebu">Carcar</option>
            <option value="Cebu City" data-province="Cebu">Cebu City</option>
            <option value="Danao" data-province="Cebu">Danao</option>
            <option value="Lapu-Lapu" data-province="Cebu">Lapu-Lapu</option>
            <option value="Mandaue" data-province="Cebu">Mandaue</option>
            <option value="Naga (Cebu)" data-province="Cebu">Naga</option>
            <option value="Talisay (Cebu)" data-province="Cebu">Talisay</option>
            <option value="Toledo" data-province="Cebu">Toledo</option>
            
            <!-- Bohol -->
            <option value="Tagbilaran" data-province="Bohol">Tagbilaran</option>
            
            <!-- Negros Oriental -->
            <option value="Bais" data-province="Negros Oriental">Bais</option>
            <option value="Bayawan" data-province="Negros Oriental">Bayawan</option>
            <option value="Canlaon" data-province="Negros Oriental">Canlaon</option>
            <option value="Dumaguete" data-province="Negros Oriental">Dumaguete</option>
            <option value="Guihulngan" data-province="Negros Oriental">Guihulngan</option>
            <option value="Tanjay" data-province="Negros Oriental">Tanjay</option>
            
            <!-- Leyte -->
            <option value="Baybay" data-province="Leyte">Baybay</option>
            <option value="Ormoc" data-province="Leyte">Ormoc</option>
            <option value="Tacloban" data-province="Leyte">Tacloban</option>
            
            <!-- Southern Leyte -->
            <option value="Maasin" data-province="Southern Leyte">Maasin</option>
            
            <!-- Eastern Samar -->
            <option value="Borongan" data-province="Eastern Samar">Borongan</option>
            
            <!-- Samar -->
            <option value="Calbayog" data-province="Samar">Calbayog</option>
            <option value="Catbalogan" data-province="Samar">Catbalogan</option>
            
            <!-- Zamboanga del Norte -->
            <option value="Dapitan" data-province="Zamboanga del Norte">Dapitan</option>
            <option value="Dipolog" data-province="Zamboanga del Norte">Dipolog</option>
            
            <!-- Zamboanga del Sur -->
            <option value="Pagadian" data-province="Zamboanga del Sur">Pagadian</option>
            <option value="Zamboanga City" data-province="Zamboanga del Sur">Zamboanga City</option>
            
            <!-- Bukidnon -->
            <option value="Malaybalay" data-province="Bukidnon">Malaybalay</option>
            <option value="Valencia" data-province="Bukidnon">Valencia</option>
            
            <!-- Lanao del Norte -->
            <option value="Iligan" data-province="Lanao del Norte">Iligan</option>
            
            <!-- Misamis Occidental -->
            <option value="Oroquieta" data-province="Misamis Occidental">Oroquieta</option>
            <option value="Ozamiz" data-province="Misamis Occidental">Ozamiz</option>
            <option value="Tangub" data-province="Misamis Occidental">Tangub</option>
            
            <!-- Misamis Oriental -->
            <option value="Cagayan de Oro" data-province="Misamis Oriental">Cagayan de Oro</option>
            <option value="El Salvador" data-province="Misamis Oriental">El Salvador</option>
            <option value="Gingoog" data-province="Misamis Oriental">Gingoog</option>
            
            <!-- Davao del Norte -->
            <option value="Panabo" data-province="Davao del Norte">Panabo</option>
            <option value="Samal" data-province="Davao del Norte">Samal</option>
            <option value="Tagum" data-province="Davao del Norte">Tagum</option>
            
            <!-- Davao del Sur -->
            <option value="Davao City" data-province="Davao del Sur">Davao City</option>
            <option value="Digos" data-province="Davao del Sur">Digos</option>
            
            <!-- Davao Oriental -->
            <option value="Mati" data-province="Davao Oriental">Mati</option>
            
            <!-- South Cotabato -->
                        <option value="General Santos" data-province="South Cotabato">General Santos</option>
            <option value="Koronadal" data-province="South Cotabato">Koronadal</option>
            
            <!-- Cotabato -->
            <option value="Kidapawan" data-province="Cotabato">Kidapawan</option>
            
            <!-- Sultan Kudarat -->
            <option value="Tacurong" data-province="Sultan Kudarat">Tacurong</option>
            
            <!-- Agusan del Norte -->
            <option value="Butuan" data-province="Agusan del Norte">Butuan</option>
            <option value="Cabadbaran" data-province="Agusan del Norte">Cabadbaran</option>
            
            <!-- Agusan del Sur -->
            <option value="Bayugan" data-province="Agusan del Sur">Bayugan</option>
            
            <!-- Surigao del Norte -->
            <option value="Surigao City" data-province="Surigao del Norte">Surigao City</option>
            
            <!-- Surigao del Sur -->
            <option value="Bislig" data-province="Surigao del Sur">Bislig</option>
            <option value="Tandag" data-province="Surigao del Sur">Tandag</option>
            
            <!-- Basilan -->
            <option value="Lamitan" data-province="Basilan">Lamitan</option>
            
            <!-- Lanao del Sur -->
            <option value="Marawi" data-province="Lanao del Sur">Marawi</option>
            
            <!-- Maguindanao -->
            <option value="Cotabato City" data-province="Maguindanao del Norte">Cotabato City</option>
            
            <!-- Abra -->
            <option value="Bangued" data-province="Abra">Bangued</option>
            
            <!-- Kalinga -->
            <option value="Tabuk" data-province="Kalinga">Tabuk</option>
            
            <!-- Mountain Province -->
            <option value="Bontoc" data-province="Mountain Province">Bontoc</option>
            
            <!-- Ifugao -->
            <option value="Lagawe" data-province="Ifugao">Lagawe</option>
            
            <!-- Batanes -->
            <option value="Basco" data-province="Batanes">Basco</option>
            
            <!-- Nueva Vizcaya -->
            <option value="Bayombong" data-province="Nueva Vizcaya">Bayombong</option>
            
            <!-- Aurora -->
            <option value="Baler" data-province="Aurora">Baler</option>
            
            <!-- Catanduanes -->
            <option value="Virac" data-province="Catanduanes">Virac</option>
            
            <!-- Camarines Norte -->
            <option value="Daet" data-province="Camarines Norte">Daet</option>
            
            <!-- Marinduque -->
            <option value="Boac" data-province="Marinduque">Boac</option>
            
            <!-- Occidental Mindoro -->
            <option value="Mamburao" data-province="Occidental Mindoro">Mamburao</option>
            
            <!-- Romblon -->
            <option value="Romblon" data-province="Romblon">Romblon</option>
            
            <!-- Aklan -->
            <option value="Kalibo" data-province="Aklan">Kalibo</option>
            
            <!-- Antique -->
            <option value="San Jose" data-province="Antique">San Jose</option>
            
            <!-- Guimaras -->
            <option value="Jordan" data-province="Guimaras">Jordan</option>
            
            <!-- Siquijor -->
            <option value="Siquijor" data-province="Siquijor">Siquijor</option>
            
            <!-- Biliran -->
            <option value="Naval" data-province="Biliran">Naval</option>
            
            <!-- Northern Samar -->
            <option value="Catarman" data-province="Northern Samar">Catarman</option>
            
            <!-- Zamboanga Sibugay -->
            <option value="Ipil" data-province="Zamboanga Sibugay">Ipil</option>
            
            <!-- Camiguin -->
            <option value="Mambajao" data-province="Camiguin">Mambajao</option>
            
            <!-- Davao de Oro -->
            <option value="Nabunturan" data-province="Davao de Oro">Nabunturan</option>
            
            <!-- Davao Occidental -->
            <option value="Malita" data-province="Davao Occidental">Malita</option>
            
            <!-- Sarangani -->
            <option value="Alabel" data-province="Sarangani">Alabel</option>
            
            <!-- Dinagat Islands -->
            <option value="San Jose" data-province="Dinagat Islands">San Jose</option>
            
            <!-- Apayao -->
            <option value="Kabugao" data-province="Apayao">Kabugao</option>
            
            <!-- Quirino -->
            <option value="Cabarroguis" data-province="Quirino">Cabarroguis</option>
            
            <!-- Maguindanao del Sur -->
            <option value="Buluan" data-province="Maguindanao del Sur">Buluan</option>
            
            <!-- Sulu -->
            <option value="Jolo" data-province="Sulu">Jolo</option>
            
            <!-- Tawi-Tawi -->
            <option value="Bongao" data-province="Tawi-Tawi">Bongao</option>
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
    const provinceSelect = document.getElementById('province');
    const citySelect = document.getElementById('city');
    const allCityOptions = Array.from(citySelect.querySelectorAll('option[data-province]'));
    
    // Function to filter cities based on selected province
    function filterCities() {
        const selectedProvince = provinceSelect.value;
        
        // Remove all city options except the first one (placeholder)
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        
        if (selectedProvince) {
            // Add only cities that match the selected province
            allCityOptions.forEach(option => {
                if (option.dataset.province === selectedProvince) {
                    citySelect.appendChild(option.cloneNode(true));
                }
            });
        }
        
        // Reset city selection
        citySelect.value = '';
        
        // If there's an old value (from validation error), try to select it
        const oldCity = '{{ old("city") }}';
        if (oldCity && selectedProvince) {
            const matchingOption = citySelect.querySelector(`option[value="${oldCity}"]`);
            if (matchingOption) {
                citySelect.value = oldCity;
            }
        }
    }
    
    // Listen for province changes
    provinceSelect.addEventListener('change', filterCities);
    
    // Initialize on page load if province is already selected
    const oldProvince = '{{ old("province") }}';
    if (oldProvince) {
        provinceSelect.value = oldProvince;
        filterCities();
    }
});
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
