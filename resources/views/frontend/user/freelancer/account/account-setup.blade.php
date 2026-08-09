@extends('frontend.layout.master')
@section('site_title', __('Account Setup'))
@section('content')
    <!-- Account Setup area Starts -->
    <div class="account-area pat-100 pab-100">
        <div class="container">
            <div class="setup-header setup-top-border">
                <div class="setup-header-flex">
                    <div class="setup-header-left">
                        <h4 class="setup-header-title">
                            {{ get_static_option('account_page_title') ?? __('Setup Your Account') }}</h4>
                    </div>
                    <!-- <div class="setup-header-right">
                            <a href="{{ route('homepage') }}" class="setup-header-skip">{{ get_static_option('account_page_skip_title') ?? __('Skip') }}</a>
                        </div> -->
                </div>


    <style>
        .identity-verifying-flex {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            gap: 10px;
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
</style>

                <div class="single-profile-settings">
                    <div class="single-profile-settings-header">
                        <div class="single-profile-settings-header-flex">
                            <x-form.form-title :title="__('Profile Info')"
                                :class="'single-profile-settings-header-title'" />
                            <div>
                                @include('frontend.user.partials.profile_completed_progressbar')
                            </div>
                        </div>
                        <p class="mt-2">
                            {{ __(key: 'Please complete the following four steps to complete your profile setup. This will help us to better understand your skills and experience.') }}
                        </p>
                    </div>
                  
                    <div class="identity-verifying-form custom-form profile-border-top">
                        <div class="row g-3"> {{-- g-3 = gap between columns --}}
                            
                            <!-- Step 1 -->
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

                            <!-- Step 2 -->
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

                            <!-- Step 3 -->
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

                            <!-- Step 4 -->
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
            </div>
            <div class="setup-wrapper setup-top-border setup-bottom-border">
                <div class="setup-wrapper-flex">
                    <div>
                        @include('frontend.user.freelancer.account.sidebar')
                    </div>
                    <div>
                        @include('frontend.user.freelancer.account.introduction')
                        @include('frontend.user.freelancer.account.experience.experience')
                        @include('frontend.user.freelancer.account.education.education')
                        @include('frontend.user.freelancer.account.work.work')
                        @include('frontend.user.freelancer.account.skill.skill')
                        @include('frontend.user.freelancer.account.hourly.hourly-rate')
                        @include('frontend.user.freelancer.account.pre-next')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Account Setup area end -->
@endsection

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

{{--todo register script--}}
@section('script')
    @include('frontend.user.freelancer.account.account-setup-js')
@endsection