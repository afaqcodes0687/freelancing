@php use plugins\PageBuilder\PageBuilderSetup; @endphp
@extends('frontend.layout.master')

@section('site_title', __('Referral Program'))
@section('meta_title')
    {{ __('Referral Program - Right Freelancer | Build Strong Collaborations') }}
@endsection

@section('meta_description')
    {{ __('Explore Referral Program opportunities with Right Freelancer. Join hands to grow together through trust, support, and innovation.') }}
@endsection

<style>
    .escrow-policy-banner {
        background-color: #309400;
        padding: 60px 0 110px;
        color: white;
        text-align: center;
    }

    .escrow-title {
        font-size: 28px;
        font-weight: 700;
        color: white;
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

    .referral-section p {
        font-size: 14px;
    }

    .referral-section h5 {
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

    @media (max-width: 768px) {
        .affiliate-link-btn {
            margin-top: 10px;
            display: inline-block;
        }

        .escrow-title {
            font-size: 24px;
        }
    }
</style>

@section('content')
    <div class="banner-inner-area border-top pat-20 pab-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="banner-inner-contents">
                        <ul class="inner-menu">
                            <li class="list"><a href="{{ url('/') }}">Home</a></li>
                            <li class="list">Referral Program</li>
                        </ul>
                        <h2 class="banner-inner-title">Referral Program</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Full-width green section --}}
    <div class="escrow-policy-banner w-100">
        <div class="container">
            <div class="row align-items-center">
                {{-- Left content --}}
                <div class="col-lg-8 text-lg-start text-center">
                    @if(Auth::check())
                        @php
                            $user = Auth::user();
                            $totalEarnings = $user->getTotalReferralEarnings();
                            $remainingPotential = $user->getRemainingReferralPotential();
                            $pendingCount = $user->getPendingReferralsCount();
                            $completedCount = $user->getCompletedReferralsCount();
                        @endphp

                        <h2 class="escrow-title">{{ $user->first_name }} {{ $user->last_name }}, claim the credit for referring friends <br>
                        to Right Freelancer</h2>
                        <p class="text-start" style="font-size: 16px; color: #fff;">
                            Earn up to $500 in Right Freelancer Credits — up to $100 from per referral.
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
                                style="color: #e5e5e5; text-decoration: underline;">
                                <em><b>Terms and conditions apply</b></em>
                            </a>
                        </p>
                    @else
                        <h2 class="escrow-title">First things first.</h2>
                        <h2 class="escrow-title">Log in to start referring friends to Right Freelancer</h2>
                        <!-- <p class="text-start" style="font-size: 16px; color: #fff;">
                            They’ll get 10% off their first order and you can <b>earn up to $500 in Right
                                Freelancer <br>
                                Credits</b> — up to $100 from each referral.
                        </p> -->
                        <div class="text-start mt-3">
                            <a href="{{ route('user.login', ['return' => 'referral-program']) }}" class="btn btn-light text-success fw-bold"
                                style="background-color:#fff; padding: 10px 20px; border-radius: 5px;">
                                Sign In
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Right image --}}
                <div class="col-lg-4 text-center mt-4 mt-lg-0">
                    <img src="{{ asset('assets/uploads/partnerimage/refer-a-friend.jpg') }}" class="img-fluid rounded"
                        alt="Refer & Earn">
                </div>
            </div>
        </div>
    </div>
    {{-- Referral card overlapping --}}
    @if(Auth::check())
        <div class="referral-card-wrapper">
            <div class="referral-card">
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

                <hr class="my-4">

                <h6 class="fw-semibold mb-2">Or share your personal referral link</h6>
                @php
                    $referralLink = route('user.register', ['ref' => Auth::user()->referral_code]);
                @endphp
               <div class="input-group mb-3 position-relative">
                    <input type="text" class="form-control" id="referralLink" readonly value="{{ $referralLink }}">
                    <button class="btn btn-outline-secondary" type="button" onclick="copyReferral(this)">Copy</button>
                    <div id="copiedToast" class="copied-toast position-absolute end-0 me-2 fw-bold">Copied!</div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($referralLink) }}" target="_blank"
                        class="btn-success btn-icon" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>

                    <!-- Twitter -->
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($referralLink) }}" target="_blank"
                        class="btn-primary btn-icon" title="Twitter" style="background-color: #1DA1F2; border-color: #1DA1F2;">
                        <i class="fab fa-twitter"></i>
                    </a>

                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($referralLink) }}" target="_blank"
                        class="btn-primary btn-icon" title="LinkedIn" style="background-color: #0077B5; border-color: #0077B5;">
                        <i class="fab fa-linkedin-in"></i>
                    </a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($referralLink) }}" target="_blank"
                        class="btn-primary btn-icon" title="Facebook" style="background-color: #1877F2; border-color: #1877F2;">
                        <i class="fab fa-facebook-f"></i>
                    </a>

                    <!-- Copy Link -->
                    <a href="javascript:void(0);" class="btn-dark btn-icon copy-profile-link" title="Copy Link"
                        data-link="{{ $referralLink }}">
                        <i class="fas fa-link"></i>
                    </a>
                </div>

                <!-- Referral Statistics -->
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="fw-bold mb-3">Your Referral Statistics</h6>
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-2">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-success">${{ number_format($totalEarnings, 2) }}</div>
                                <small class="text-muted">Total Earned</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-primary">${{ number_format($remainingPotential, 2) }}</div>
                                <small class="text-muted">Remaining Potential</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-warning">{{ $pendingCount }}</div>
                                <small class="text-muted">Pending Referrals</small>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-2">
                            <div class="border rounded p-2">
                                <div class="fw-bold text-success">{{ $completedCount }}</div>
                                <small class="text-muted">Completed Referrals</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <section class="referral-section py-5">
        <div class="container">
            <p class="text-uppercase fw-bold text-orange small mb-1">From referral to reward</p>

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <h2 class="section-title m-0">It’s easy to earn with referrals</h2>
                <a href="{{ url('terms-of-service') }}" class="btn btn-light fw-bold"
                    style="background-color:#309400; color:white; padding: 10px 20px; border-radius: 5px;">Read full
                    terms</a>
            </div>

            <div class="row justify-content-center">
                <!-- Invite Friends -->
                <div class="col-md-4 mb-2">
                    <img src="{{ asset('assets/uploads/refrrel-program/14722347.png') }}" alt="Invite Friends"
                        class="mb-3" width="50">
                    <h5 class="fw-bold">Invite friends</h5>
                    <p class="text-muted small">
                        Refer friends to Right Freelancer through email, with your own personal referral link, or by spreading the word on social.
                    </p>
                </div>

                <!-- They Get a Discount -->
                <div class="col-md-4 mb-2">
                    <img src="{{ asset('assets/uploads/refrrel-program/pngtree-icon-concept-for-inviting-friends-or-adding-vector-png-image_15917171.png') }}" alt="Get Discount"
                        class="img-fluid"
                        style="max-width: 100px;">

                    <h5 class="fw-bold">They get a discount</h5>
                    <p class="text-muted small">
                        When your referrals join Right Freelancer, they’ll get 10% off their first order.
                    </p>
                </div>

                <!-- You Get the Credit -->
                <div class="col-md-4 mb-2">

                 <img src="{{ asset('assets/uploads/refrrel-program/pngtree-icon-concept-for-invitations-to-invite-friends-or-add-vector-png-image_13066505.png') }}" alt="Get Discount"
                        class="img-fluid"
                        style="max-width: 100px;">

                    <h5 class="fw-bold">You get the credit</h5>
                    <p class="text-muted small">
                        Once they complete their order, you’ll get 10% of their purchase in RF Credits to use on your next order.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="affiliate-invite-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <!-- Left Text -->
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2 class="escrow-title text-dark fw-bold"> Are you looking to boost your earnings and become a
                        RightFreelancer Affiliate?</h2>
                </div>

                <!-- Right Text + Button -->
                <div class="col-md-6">
                    <p class="mb-4">
                        Join the RightFreelancer Affiliates Program. It’s free to get started and you earn right from the
                        moment your traffic converts. There's a match for every need, so share away…
                    </p>

                    <a href="{{ route('affiliate.register') }}" class="affiliate-link-btn">
                        Affiliates Program <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
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