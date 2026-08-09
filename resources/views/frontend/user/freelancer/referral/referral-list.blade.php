@extends('frontend.layout.master')
@section('site_title',__('Referral'))
@section('style')

<style>
     .total_balance{background-color: #e3e1ff !important;}

    .escrow-policy-banner {
        background-color: #309400;
        padding: 60px 0 160px;
        color: white;
        text-align: center;
    }

    .escrow-title {
        font-size: 24px;
        font-weight: 700;
    }
    .text-dark {
    color: #212529 !important;
}


.referral-card-wrapper {
        margin-top: -120px;
        margin-bottom: 60px;
        z-index: 2;
        position: relative;
    }

    .referral-card {
        background-color: #fff;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        padding: 30px;
        max-width: 700px;
        margin: auto;
    }

    .referral-card .btn-sm {
        width: 36px;
        height: 36px;
        padding: 0;
    }

    #copiedToast {
        font-size: 14px;
        transition: opacity 0.3s ease;
    }

    .copied-toast {
        top: 100%;
        display: none;
        color: #309400;
        font-size: 14px;
    }

    .referral-section {
        background-color: #fff;
    }

    .text-orange {
        color: #309400;
        /* Fiverr's referral section color or use your theme */
    }

    .referral-section img {
        max-height: 50px;
    }

    .referral-section p {
        font-size: 14px;
    }

    .referral-section h5 {
        margin-top: 10px;
        font-size: 18px;
    }

    .affiliate-invite-section {
        background-color: #d4edda;
    }

    .affiliate-link-btn {
        font-weight: bold;
        color: #000;
        text-decoration: none;
        transition: color 0.2s ease;
        display: inline-block;
    }

    .affiliate-link-btn:hover {
        color: #309400;
        /* Green hover effect */
    }

    .modal-content.radius-10 {
        border-radius: 10px;
    }

    .btn-sm {
        padding: 3px 10px;
        font-size: 13px;
    }

    .btn-icon {
        width: 40px;
        height: 40px;
        padding: 0;
        font-size: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

  


    #inviteCount {
        min-width: 28px;
        height: 28px;
        line-height: 28px;
        text-align: center;
        background-color: #309400;
    }

    /* Badge styles */
    .badge-completed {
        border: 2px solid #28a745 !important;
        background-color: #f8fff9;
    }

    .badge-unlocked {
        filter: drop-shadow(0 0 8px rgba(40, 167, 69, 0.3));
        transform: scale(1.05);
        transition: all 0.3s ease;
    }

    .referral-badge {
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .referral-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .referral-badge .progress {
        height: 8px;
        border-radius: 4px;
        background-color: #e9ecef;
    }

    .referral-badge .progress-bar {
        transition: width 0.6s ease;
    }

    .badge-icon {
        max-height: 50px;
        transition: all 0.3s ease;
    }

</style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Referral')" :innerTitle="__('Referral')"/>
        <!-- Profile Settings area Starts -->
        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')
                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">
                            <div class="single-profile-settings">
                                <div class="mt-2 p-3 rounded">
                                    <div class="col-lg-8 text-lg-start text-center">
                                        @if(Auth::check())
                                            @php
                                                $user = Auth::user();
                                                $totalEarnings = $user->getTotalReferralEarnings();
                                                $remainingPotential = $user->getRemainingReferralPotential();
                                                $pendingCount = $user->getPendingReferralsCount();
                                                $completedCount = $user->getCompletedReferralsCount();
                                            @endphp

                                            <h2 class="escrow-title">{{ $user->first_name }} {{ $user->last_name }}, take the credit
                                                for referring friends <br> to Right Freelancer</h2>
                                            <p class="text-start" style="font-size: 16px;">
                                                Earn up to $500 in Right Freelancer Credits — up to $100 from each referral.
                                                @if($totalEarnings > 0)
                                                    <br><strong>You've earned ${{ number_format($totalEarnings, 2) }} so far!</strong>
                                                    @if($remainingPotential > 0)
                                                        <br><em>You can still earn up to ${{ number_format($remainingPotential, 2) }} more.</em>
                                                    @else
                                                        <br><em>You've reached the maximum earnings limit!</em>
                                                    @endif
                                                @endif
                                            </p>
                                            <p>
                                                <a href="{{ url('terms-of-service') }}" target="_blank"
                                                    style="text-decoration: underline;">
                                                    <em><b>Terms and conditions apply</b></em>
                                                </a>
                                            </p>
                                        @else
                                            <h2 class="escrow-title">First things first.</h2>
                                            <h2 class="escrow-title">Sign in to start referring friends to Right Freelancer</h2>
                                            <p class="text-start" style="font-size: 16px;">
                                                They’ll get 10% off their first order and you can <b>earn up to $500 in Right
                                                    Freelancer <br>
                                                    Credits</b> — up to $100 from each referral.
                                            </p>
                                            <div class="text-start mt-3">
                                                <a href="{{ route('user.login') }}" class="btn btn-light text-success fw-bold"
                                                    style="background-color:#fff; padding: 10px 20px; border-radius: 5px;">
                                                    Sign In
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    <h5 class="fw-bold mb-3">Invite friends through email</h5>
                <form id="referralForm" method="POST" action="{{ route('referral.send.invitation') }}">
                    @csrf
                    <div class="d-flex mb-3">
                        <input type="text" name="emails" id="emails" class="form-control me-2" placeholder="Add Email Address"
                            required>
                        <button type="submit" id="sendBtn" class="btn btn-light fw-bold"
                            style="background-color:#309400; color:white; padding: 10px 20px; border-radius: 5px;">
                            <span id="sendBtnText">Send</span>
                            <span id="sendBtnSpinner" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>

                    @php
                        $count = \App\Models\ReferralInvitation::where('user_id', Auth::id())->count();
                    @endphp

                    <p class="d-flex align-items-center gap-2 fw-semibold">
                        You have sent
                        <span id="inviteCount"
                            class="rounded-circle text-white d-inline-flex justify-content-center align-items-center"
                            style="width: 28px; height: 28px; font-size: 14px;">
                            {{ $count }}
                        </span>
                        {{ $count === 1 ? 'invitation' : 'invitations' }}.
                    </p>
                    <!-- <small class="text-muted">Separate emails with commas</small> -->
                    <div id="formMessage" class="mt-2" style="display: none;"></div>
                </form>
                                    <h6 class="fw-bold mb-3">Your Referral Statistics</h6>
                                    <div class="row text-center">
                                        <div class="col-6 col-md-3 mb-2">
                                            <div class="myJob-wrapper-single-balance total_balance">
                                                <div class="rounded p-2">
                                                    <div class="fw-bold text-success">${{ number_format($totalEarnings, 2) }}</div>
                                                    <small class="text-muted">Total Earned</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2">
                                            <div class="myJob-wrapper-single-balance">
                                                <div class="rounded p-2">
                                                    <div class="fw-bold text-primary">${{ number_format($remainingPotential, 2) }}</div>
                                                    <small class="text-muted">Remaining Potential</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2">
                                             <div class="myJob-wrapper-single-balance">
                                                <div class="rounded p-2">
                                                    <div class="fw-bold text-warning">{{ $pendingCount }}</div>
                                                    <small class="text-muted">Pending Referrals</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-2">
                                            <div class="myJob-wrapper-single-balance total_balance">
                                                <div class="rounded p-2">
                                                    <div class="fw-bold text-success">{{ $completedCount }}</div>
                                                    <small class="text-muted">Completed Referrals</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="single-profile-settings mt-4">
                                <h3 class="escrow-title mb-3">Referral Summary</h3>
                                <div class="row text-center">
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="myJob-wrapper-single-balance total_balance">
                                            <div class="rounded p-2">
                                                <div class="fw-bold text-success">{{ $totalInvited }}</div>
                                                <small class="text-muted">Total Invited Friends</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="myJob-wrapper-single-balance">
                                            <div class="rounded p-2">
                                                <div class="fw-bold text-primary">{{ $rewardedFriends }}</div>
                                                <small class="text-muted">Rewarded Friends</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="myJob-wrapper-single-balance">
                                            <div class="rounded p-2">
                                                <div class="fw-bold text-warning">${{ number_format($totalEarning, 2) }}</div>
                                                <small class="text-muted">Total Earning</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 mb-2">
                                        <div class="myJob-wrapper-single-balance total_balance">
                                            <div class="rounded p-2">
                                                <div class="fw-bold text-success">{{ $rewardsInProgress }}</div>
                                                <small class="text-muted">Rewards in Progress</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="single-profile-settings mt-4"> 
                                <h3 class="escrow-title">Collect Badges and Earn More</h3>
                                <h6 class="text-center mb-3 mt-3">
                                    Unlock all these badges to earn your place with us as Right Freelancer, <a href="#">Learn More</a>
                                </h6>
                                <div class="row g-3">
                                    @foreach($badgeProgress as $badge)
                                    <div class="col-12 col-sm-6">
                                        <div class="referral-badge text-center p-3 border rounded h-100 {{ $badge['is_completed'] ? 'badge-completed' : '' }}">
                                            <img src="{{ asset('assets/uploads/refrrel-program/' . ($badge['is_completed'] ? $badge['completed_icon'] : $badge['icon'])) }}" 
                                                 alt="{{ $badge['name'] }}" 
                                                 class="badge-icon mb-2 {{ $badge['is_completed'] ? 'badge-unlocked' : '' }}">
                                            <h6 class="fw-bold {{ $badge['is_completed'] ? 'text-success' : '' }}">{{ $badge['name'] }}</h6>
                                            <p class="small">To earn this badge and get rewarded with ${{ $badge['reward'] }} refer us to {{ $badge['required'] }} of your friends.</p>
                                            <div class="progress mb-1">
                                                <div class="progress-bar {{ $badge['is_completed'] ? 'bg-success' : 'bg-warning' }}" 
                                                     style="width: {{ $badge['progress_percentage'] }}%"></div>
                                            </div>
                                            <small>{{ $badge['current'] }}/{{ $badge['required'] }}</small>
                                            @if($badge['is_completed'])
                                                <div class="mt-2">
                                                    <span class="badge bg-success">✓ Unlocked</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="single-profile-settings mt-4"> 
                                <div class="container my-4">
                                    <h5 class="mb-3 text-dark fw-semibold">Referrals List</h5>
                                    
                                    <div class="text-center">
                                        <span class="text-dark">
                                            Refer your friends and share with them new trading experience
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if(get_static_option('project_enable_disable') != 'disable')
                                <div class="single-profile-settings mt-4">
                                    <div class="single-profile-settings-header">
                                        <div class="single-profile-settings-header-flex">
                                            <x-form.form-title :title="__('All Referrals')" :class="'single-profile-settings-header-title'" />
                                        </div>
                                    </div>
                                    <div class="single-profile-settings-inner profile-border-top">
                                        <div class="custom_table style-04">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th>{{ __('ID') }}</th>
                                                    <th>{{ __('Refered Name') }}</th>
                                                    <th>{{ __('Refered Email') }}</th>
                                                    <th>{{ __('Refered Status') }}</th>
                                                    <th>{{ __('Refered Code') }}</th>
                                                    <th>{{ __('Reward Amount') }}</th>
                                                    <th>{{ __('Create At') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($referrals as $referral)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $referral->referred ? ($referral->referred->first_name ?? 'N/A' . ' ' . ($referral->referred->last_name ?? 'N/A')) : 'N/A' }}</td>
                                                        <td>{{ $referral->referred ? $referral->referred->email : 'N/A' }}</td>
                                                        <td>{{ ucfirst($referral->status) }}</td>
                                                        <td>{{ ucfirst($referral->referral_code) }}</td>
                                                        <td>${{ number_format($referral->reward_amount ?? 0, 2) }}</td>
                                                        <td>{{ $referral->created_at->format('d M, Y') }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('referralForm');
            const sendBtn = document.getElementById('sendBtn');
            const sendBtnText = document.getElementById('sendBtnText');
            const sendBtnSpinner = document.getElementById('sendBtnSpinner');
            const formMessage = document.getElementById('formMessage');
            const emailsInput = document.getElementById('emails');

            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const emails = emailsInput.value.trim();
                    if (!emails) {
                        showMessage('Please enter at least one email address.', 'danger');
                        return;
                    }

                    sendBtn.disabled = true;
                    sendBtnText.style.display = 'none';
                    sendBtnSpinner.style.display = 'inline-block';

                    const formData = new FormData();
                    formData.append('emails', emails);
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showMessage(data.message, 'success');

                                // Reload the page after a short delay (e.g., 1 second)
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            } else {
                                showMessage(data.message, 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage('An error occurred while sending invitations. Please try again.', 'danger');
                        })
                        .finally(() => {
                            sendBtn.disabled = false;
                            sendBtnText.style.display = 'inline-block';
                            sendBtnSpinner.style.display = 'none';
                        });
                });
            }

            function showMessage(message, type) {
                formMessage.style.display = 'block';
                formMessage.className = `mt-2 alert alert-${type}`;
                formMessage.textContent = message;

                if (type === 'success') {
                    setTimeout(() => {
                        formMessage.style.display = 'none';
                    }, 3000);
                }
            }
        });
    </script>

   <script>
        function copyReferral(button) {
            const referralInput = document.getElementById('referralLink');
            const copiedToast = document.getElementById('copiedToast');

            referralInput.select();
            referralInput.setSelectionRange(0, 99999); // For mobile

            try {
                document.execCommand('copy');

                // Show message
                copiedToast.style.opacity = 1;

                // Hide after 2 seconds
                setTimeout(() => {
                    copiedToast.style.opacity = 0;
                }, 2000);
            } catch (err) {
                alert('Copy failed: ' + err);
            }
        }
    </script>
@endsection
