@extends('frontend.layout.master')
@section('site_title',__('Wallet History'))
@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <style>
        .single-profile-settings-flex {
            justify-content: space-between;
        }
        .single-profile-settings-contents .single-profile-settings-contents-upload-btn {
            padding: 0;
        }
        .single-profile-settings .single-profile-settings-thumb {
            max-width: unset;
        }
        .balance-wallet {
            color: var(--paragraph-color);
        }
        .balance-wallet strong {
            color: var(--heading-color);
        }
        .single-profile-settings-thumb {
            width:unset;
        }

        .identity-verifying-list {
            flex: 1 1 0;
            min-width: 200px;  
            max-width: 250px;
            padding: 10px;
            box-sizing: border-box;
            background: #f8f8f8;
            border: 1px solid #ccc;
            border-radius: 5px;
            cursor: default;
            pointer-events: none;
        }
        .identity-verifying-list-contents-details-title {
            font-size: 14px;
            /* white-space: nowrap; */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .identity-verifying-list-contents-flex {
            align-items: center;
            display: flex;
            gap: 8px;
        }
        .identity-verifying-list.completed {
            pointer-events: none;
            background-color: #d4edda;
            border-color: #309400;
            color: #155724;
            cursor: default;
        }

        .identity-verifying-list.completed i {
            color: #309400;
        }

        /* New styles for clickable tabs */
        .identity-verifying-list.clickable {
            cursor: pointer;
            pointer-events: auto;
            transition: all 0.3s ease;
        }

        .identity-verifying-list.clickable:hover {
            background-color: #e9ecef;
            border-color: #309400;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .identity-verifying-list.clickable:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Warning message styles */
        .step-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 10px 0;
            display: none;
            font-size: 14px;
        }

        .step-warning.show {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .identity-verifying-list.locked {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
        }

        .identity-verifying-list.locked i {
            color: #6c757d;
        }

        /* Bank form polish */
        .bank-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
        }
        .bank-card .card-title{
            font-weight: 700;
        }
        .form-label .required{
            color:#dc3545;
        }
        .input-group-text{
            background: #f8f9fa;
        }
        .help-text{
            color:#6c757d;
            font-size: 12px;
        }
        #iban_field, #account_field{
            transition: all .2s ease;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Wallet History')" :innerTitle="__('Wallet History')"/>

        <!-- Profile Settings area Starts -->
        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')
                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">
                            <div class="single-profile-settings">
                                <div class="single-profile-settings-header">
                                    <div class="single-profile-settings-header-flex">
                                        <x-form.form-title :title="__('Profile Info')" :class="'single-profile-settings-header-title'" />
                                        <div>
                                            @include('frontend.user.partials.profile_completed_progressbar')
                                        </div>
                                    </div>
                                    <p class="mt-2">{{ __(key: 'Please complete the following four steps to complete your profile setup. This will help us to better understand your skills and experience.') }}</p>
                                     <!-- <strong>✅ Identity verification has been successfully completed. Please Your identity is now verified by the admin</strong><br> -->
                                </div>
                                <div class="identity-verifying-form custom-form profile-border-top">
                                   <div class="row g-3">
                                        <!-- Step 1: Personal Information -->
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="identity-verifying-list custom-radio {{ $step1Complete ? 'completed' : '' }}" 
                                                data-step="1" 
                                                data-step-name="{{ __('Personal Information') }}"
                                                data-url="{{ route('freelancer.profile') }}"
                                                data-required-steps="[]">
                                                <div class="identity-verifying-list-flex">
                                                    <div class="identity-verifying-list-contents">
                                                        <div class="identity-verifying-list-contents-flex">
                                                            <div class="identity-verifying-list-contents-icon">
                                                                <i class="fa-solid fa-user"></i>
                                                            </div>
                                                            <div class="identity-verifying-list-contents-details">
                                                                <h5 class="identity-verifying-list-contents-details-title">{{ __('Personal Information') }}</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Step 2: Account Setup -->
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="identity-verifying-list custom-radio {{ $step2Complete ? 'completed' : '' }} {{ $step1Complete && !$step2Complete ? 'clickable' : '' }}" 
                                                data-step="2" 
                                                data-step-name="{{ __('Account Setup') }}"
                                                data-url="{{ route('freelancer.account.setup') }}"
                                                data-required-steps="[1]">
                                                <div class="identity-verifying-list-flex">
                                                    <div class="identity-verifying-list-contents">
                                                    <div class="identity-verifying-list-contents-flex">
                                                        <div class="identity-verifying-list-contents-icon">
                                                        <i class="fa-solid fa-user-gear"></i>
                                                        </div>
                                                        <div class="identity-verifying-list-contents-details">
                                                        <h5 class="identity-verifying-list-contents-details-title">{{ __('Account Setup') }}</h5>
                                                        </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Step 3: Wallet -->
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="identity-verifying-list custom-radio {{ $step3Complete ? 'completed' : '' }} {{ $step1Complete && $step2Complete && !$step3Complete ? 'clickable' : '' }}" 
                                                data-step="3" 
                                                data-step-name="{{ __('Wallet') }}"
                                                data-url="{{ route('freelancer.wallet.history') }}"
                                                data-required-steps="[1,2]">
                                                <div class="identity-verifying-list-flex">
                                                    <div class="identity-verifying-list-contents">
                                                        <div class="identity-verifying-list-contents-flex">
                                                            <div class="identity-verifying-list-contents-icon">
                                                                <i class="fa-solid fa-wallet"></i>
                                                            </div>
                                                            <div class="identity-verifying-list-contents-details">
                                                                <h5 class="identity-verifying-list-contents-details-title">{{ __('Wallet') }}</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Step 4: Identity Verification -->
                                        <div class="col-12 col-sm-6 col-lg-3">
                                            <div class="identity-verifying-list custom-radio {{ $step4Complete ? 'completed' : '' }} {{ $step1Complete && $step2Complete && $step3Complete && !$step4Complete ? 'clickable' : '' }}" 
                                                data-step="4" 
                                                data-step-name="{{ __('Identity Verification') }}"
                                                data-url="{{ route('freelancer.identity.verification') }}"
                                                data-required-steps="[1,2,3]">
                                                <div class="identity-verifying-list-flex">
                                                    <div class="identity-verifying-list-contents">
                                                        <div class="identity-verifying-list-contents-flex">
                                                            <div class="identity-verifying-list-contents-icon">
                                                                <i class="fa-solid fa-fingerprint"></i>
                                                            </div>
                                                            <div class="identity-verifying-list-contents-details">
                                                                <h5 class="identity-verifying-list-contents-details-title">{{ __('Identity Verification') }}</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                   </div>
                                </div>
                            </div>
                            <div class="single-profile-settings mt-4" id="display_client_profile_photo">
                                <div class="d-flex flex-column flex-lg-row align-items-lg-stretch gap-3 gap-lg-4">

                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-column flex-md-row gap-3">
                                            <div class="flex-grow-1 bg-white">
                                                <h4 class="balance-wallet">{{ __('Balance:') }}
                                                <strong style="color:#309400">{{ float_amount_with_currency_symbol($earning_plus_deposit ?? 0) }}</strong>
                                                </h4>
                                                <p class="job-progress mt-2">{{ __('Earning+Deposit') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-column flex-md-row gap-2 gap-md-3">
                                        @php
                                            $user = Auth::guard('web')->user();

                                            $is_profile_complete = $step1Complete && $step2Complete && $step3Complete && $step4Complete;

                                            $completed_jobs = \App\Models\Order::where('freelancer_id', $user->id)
                                                ->where('status', '3')->count();
                                        @endphp

                                        @if($withdrawable_balance >= get_static_option('minimum_withdraw_amount') && $is_profile_complete && $completed_jobs > 0)
                                            <div class="flex-grow-1 flex-md-grow-0">
                                                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                                                    @if(moduleExists('SecurityManage') && $user->freeze_withdraw == 'freeze')
                                                        {{ __('Withdraw Request') }} 
                                                        <i class="fas fa-lock ms-1"></i>
                                                    @else
                                                        {{ __('Withdraw Request') }}
                                                    @endif
                                                </button>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1 flex-md-grow-0">
                                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#paymentGatewayModal">{{ __('Deposit to Wallet') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            @php
                                $bank_account = Modules\Wallet\Entities\BankAccount::where('user_id', auth()->id())->first();
                            @endphp

                            <div class="mt-5 d-flex justify-content-center">
                                <div class="card bank-card shadow-sm w-100 mt-4" style="max-width: 640px;">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-center mb-3">
                                            <i class="fa-solid fa-building-columns me-2"></i>
                                            <h4 class="card-title m-0">{{ __('Bank Information') }}</h4>
                                        </div>

                                        <form action="{{ route('freelancer.bank.submit') }}" method="POST" novalidate>
                                            @csrf

                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label for="country_id" class="form-label">{{ __('Bank Country') }} <span class="required">*</span></label>
                                                    <select name="country" id="country_id" class="form-control country_select2">
                                                        <option value="">{{ __('Select Country') }}</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" {{ old('country', $bank_account->country_id ?? '') == $country->id ? 'selected' : '' }}>
                                                                {{ $country->country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('country')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="bank_name" class="form-label">{{ __('Bank Name') }} <span class="required">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fa-solid fa-landmark"></i></span>
                                                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $bank_account->bank_name ?? '') }}" placeholder="{{ __('Enter Bank Name') }}" required>
                                                    </div>
                                                    <div class="help-text mt-1">{{ __('Official name of your bank') }}</div>
                                                    @error('bank_name')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="account_title" class="form-label">{{ __('Account Title') }} <span class="required">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                                                        <input type="text" class="form-control" id="account_title" name="account_title" value="{{ old('account_title', $bank_account->account_title ?? '') }}" placeholder="{{ __('Enter Account Title') }}" required>
                                                    </div>
                                                    <div class="help-text mt-1">{{ __('Name as it appears on your bank account') }}</div>
                                                    @error('account_title')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12">
                                                    <label for="swis_code" class="form-label">{{ __('SWIFT Code') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fa-solid fa-code"></i></span>
                                                        <input type="text" class="form-control" id="swis_code" name="swis_code" value="{{ old('swis_code', $bank_account->swis_code ?? '') }}" placeholder="{{ __('Enter SWIFT Code') }}">
                                                    </div>
                                                    <div class="help-text mt-1">{{ __('Used for international transfers (optional)') }}</div>
                                                    @error('swis_code')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="show_iban" name="show_iban" {{ old('show_iban', $bank_account ? $bank_account->iban_number : '') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="show_iban">{{ __('Add IBAN') }}</label>
                                                    </div>
                                                    <div class="mt-2 {{ old('show_iban', $bank_account ? $bank_account->iban_number : '') ? '' : 'd-none' }}" id="iban_field">
                                                        <label for="iban_number" class="form-label">{{ __('IBAN Number') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                                                            <input type="text" class="form-control" id="iban_number" name="iban_number" value="{{ old('iban_number', $bank_account ? $bank_account->iban_number : '') }}" placeholder="{{ __('Enter IBAN Number') }}">
                                                        </div>
                                                        @error('iban_number')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="show_account" name="show_account" {{ old('show_account', $bank_account ? $bank_account->account_number : '') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="show_account">{{ __('Add Account Number') }}</label>
                                                    </div>
                                                    <div class="mt-2 {{ old('show_account', $bank_account ? $bank_account->account_number : '') ? '' : 'd-none' }}" id="account_field">
                                                        <label for="account_number" class="form-label">{{ __('Account Number') }}</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fa-regular fa-credit-card"></i></span>
                                                            <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number', $bank_account ? $bank_account->account_number : '') }}" placeholder="{{ __('Enter Account Number') }}">
                                                        </div>
                                                        @error('account_number')
                                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-primary mt-2 py-2">
                                                            {{ $bank_account ? __('Update Bank Details') : __('Save Bank Details') }}
                                                        </button>
                                                    </div>
                                                    <div class="help-text mt-2 text-center">{{ __('Your bank details are kept private and used only for withdrawals.') }}</div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="single-profile-settings mt-4" id="display_client_profile_info">
                                <div class="single-profile-settings-header">
                                    <x-validation.error />
                                    <div class="single-profile-settings-header-flex">
                                        <x-form.form-title :title="__('Wallet History')" :class="'single-profile-settings-header-title'" />
                                        <x-search.search-in-table :id="'string_search'" :placeholder="__('Enter date to search')" />
                                    </div>
                                </div>
                                <div class="single-profile-settings-inner profile-border-top">
                                    <div class="custom_table style-04 search_result">
                                          @include('wallet::freelancer.wallet.search-result')
                                    </div>
                                </div>
                            </div>
                            <!-- Bank Information Box -->
                            <div class="single-profile-settings mt-4">
                                <div class="single-profile-settings-header">
                                    <x-form.form-title :title="__('Bank Information')" :class="'single-profile-settings-header-title'" />
                                </div>
                                <div class="single-profile-settings-inner profile-border-top">
                                    <div class="custom_table style-04">
                                        <table>
                                            <thead>
                                            <tr>
                                                <th>{{ __('Country') }}</th>
                                                <th>{{ __('Bank Name') }}</th>
                                                <th>{{ __('Account Title') }}</th>
                                                <th>{{ __('SWIFT Code') }}</th>
                                                <th>{{ __('IBAN Number') }}</th>
                                                <th>{{ __('Account Number') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($bank_account)
                                                <tr>
                                                    <td>{{ $bank_account->country->country ?? '' }}</td>
                                                    <td>{{ $bank_account->bank_name }}</td>
                                                    <td>{{ $bank_account->account_title }}</td>
                                                    <td>{{ $bank_account->swis_code }}</td>
                                                    <td>{{ $bank_account->iban_number ?? '' }}</td>
                                                    <td>{{ $bank_account->account_number ?? '' }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="6">{{ __('No bank account information found.') }}</td>
                                                </tr>
                                            @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Profile Settings area end -->
        @include('wallet::freelancer.wallet.withdraw-modal')
        <x-frontend.payment-gateway.gateway-markup :title="__('You can deposit to your wallet from the available payment gateway')"/>
    </main>
@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('wallet::freelancer.wallet.wallet-js')
    <x-frontend.payment-gateway.gateway-select-js />

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ibanCheckbox = document.getElementById('show_iban');
        const accountCheckbox = document.getElementById('show_account');
        const ibanField = document.getElementById('iban_field');
        const accountField = document.getElementById('account_field');

        ibanCheckbox.addEventListener('change', function () {
            ibanField.classList.toggle('d-none', !this.checked);
        });

        accountCheckbox.addEventListener('change', function () {
            accountField.classList.toggle('d-none', !this.checked);
        });
    });
</script>

<script>
      $(document).ready(function () {
    $('.country_select2').select2({
        dropdownParent: $('.card-body')
    });
});

</script>
<script>
  function toastr_success_js(msg){
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    toastr.success(msg, "Success!");
}

</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Step completion status
    const stepStatus = {
        1: {{ $step1Complete ? 'true' : 'false' }},
        2: {{ $step2Complete ? 'true' : 'false' }},
        3: {{ $step3Complete ? 'true' : 'false' }},
        4: {{ $step4Complete ? 'true' : 'false' }}
    };
    
    // Warning message elements
    const warningContainer = document.getElementById('step-warning-message');
    const warningText = document.getElementById('warning-text');
    
    // Function to show warning message
    function showWarning(message) {
        warningText.textContent = message;
        warningContainer.classList.add('show');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            warningContainer.classList.remove('show');
        }, 5000);
    }
    
    // Function to check if step is accessible
    function isStepAccessible(stepNumber) {
        const step = document.querySelector(`[data-step="${stepNumber}"]`);
        if (!step) return false;
        
        const requiredSteps = JSON.parse(step.getAttribute('data-required-steps') || '[]');
        
        // Check if all required steps are completed
        for (let requiredStep of requiredSteps) {
            if (!stepStatus[requiredStep]) {
                return false;
            }
        }
        
        return true;
    }
    
    // Function to get incomplete required steps
    function getIncompleteRequiredSteps(stepNumber) {
        const step = document.querySelector(`[data-step="${stepNumber}"]`);
        if (!step) return [];
        
        const requiredSteps = JSON.parse(step.getAttribute('data-required-steps') || '[]');
        const incompleteSteps = [];
        
        for (let requiredStep of requiredSteps) {
            if (!stepStatus[requiredStep]) {
                const stepName = document.querySelector(`[data-step="${requiredStep}"]`).getAttribute('data-step-name');
                incompleteSteps.push(stepName);
            }
        }
        
        return incompleteSteps;
    }
    
    // Add click event listeners to all steps
    const allSteps = document.querySelectorAll('.identity-verifying-list');
    
    allSteps.forEach(step => {
        step.addEventListener('click', function() {
            const stepNumber = parseInt(this.getAttribute('data-step'));
            const stepName = this.getAttribute('data-step-name');
            const url = this.getAttribute('data-url');
            
            // If step is completed, do nothing
            if (stepStatus[stepNumber]) {
                return;
            }
            
            // Check if step is accessible
            if (!isStepAccessible(stepNumber)) {
                const incompleteSteps = getIncompleteRequiredSteps(stepNumber);
                const warningMessage = `{{ __('Please complete the following steps first:') }} ${incompleteSteps.join(', ')}`;
                showWarning(warningMessage);
                return;
            }
            
            // If step is clickable and accessible, navigate to URL
            if (this.classList.contains('clickable') && url) {
                window.location.href = url;
            }
        });
    });
    
    // Add visual indicators for locked steps
    allSteps.forEach(step => {
        const stepNumber = parseInt(step.getAttribute('data-step'));
        
        // If step is not completed and not accessible, add locked class
        if (!stepStatus[stepNumber] && !isStepAccessible(stepNumber)) {
            step.classList.add('locked');
        }
    });
    
    // Add hover effect for locked steps to show warning
    const lockedSteps = document.querySelectorAll('.identity-verifying-list.locked');
    lockedSteps.forEach(step => {
        step.addEventListener('mouseenter', function() {
            const stepNumber = parseInt(this.getAttribute('data-step'));
            const stepName = this.getAttribute('data-step-name');
            const incompleteSteps = getIncompleteRequiredSteps(stepNumber);
            const warningMessage = `{{ __('Complete these steps first:') }} ${incompleteSteps.join(', ')}`;
            showWarning(warningMessage);
        });
        
        step.addEventListener('mouseleave', function() {
            warningContainer.classList.remove('show');
        });
    });
});
</script>





@if(session('success'))
    <script>
        toastr_success_js(@json(session('success')));
    </script>
@endif

@endsection
