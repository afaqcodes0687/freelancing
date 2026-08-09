@extends('frontend.layout.master')
@section('site_title',__('Dashboard'))
@section('style')

<style>
    .single-freelancer {
        font-family: 'Inter', 'Poppins', 'Roboto', sans-serif;
        box-shadow:
            0 2px 4px rgba(0, 0, 0, 0.04),
            0 8px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    .total_balance{background-color: #e3e1ff !important;}
    .pro-profile-badge {
        position: absolute;
        right: 8px;
        top: 6px;
        border-radius: 20px;
        background: #FAF5FF;
        color: #9e4cf4;
        font-weight: 600;
    }

    .popup-contents-close {
        border: none !important;
        outline: none !important;   
        box-shadow: none !important;   
            padding: 0;                   
        cursor: pointer;             
    }

    .single-freelancer {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        will-change: transform;
        cursor: pointer;
    }
    /* Limit skills to 3 lines with uniform height */
    .skills-list {
        height: 100px; /* Fixed height for exactly 3 lines of badges */
        overflow: hidden;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        align-content: flex-start; /* Ensure they start from the top */
    }

    /* Limit title to 2 lines with uniform height */
    .single-freelancer-author-para {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 42px; /* Fixed height for 2 lines */
        line-height: 21px;
        margin-bottom: 10px;
        font-size: 14px;
        text-align: center;
        width: 100%;
    }

    .single-freelancer-author-name {
        font-size: 18px;
        font-weight: 700;
        margin-top: 10px;
        height: 28px; /* Fixed height for 1 line name */
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        text-align: center;
        width: 100%;
        display: block;
    }

    .skills-list .badge {
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px !important;
        border-radius: 4px;
        background-color: #f8f9fa !important;
        color: #495057 !important;
        border: 1px solid #e9ecef !important;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Dashboard')" :innerTitle="__('Dashboard')"/>
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
                                        <x-form.form-title :title="__('Dashboard Info')" :class="'single-profile-settings-header-title'" />
                                        <div>
                                            @include('frontend.user.partials.profile_completed_progressbar')
                                        </div>
                                    </div>
                                </div>
                                <div class="single-profile-settings-inner profile-border-top">
                                    <div class="row">

                                        <div class="col-xxl-3 col-lg-6 col-sm-6 col-md-4">
                                            <div class="myJob-wrapper-single-balance total_balance">
                                                <div class="myJob-wrapper-single-balance-contents">
                                                    <div class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                        <h4 class="contract_single__balance-price">{{ float_amount_with_currency_symbol($total_wallet_balance) ?? 0.0 }}</h4>
                                                    </div>
                                                    <p class="myJob-wrapper-single-balance-para">{{ __('Wallet Balance') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if(get_static_option('job_enable_disable') != 'disable')
                                        <div class="col-xxl-3 col-lg-6 col-sm-6 col-md-4">
                                            <div class="myJob-wrapper-single-balance">
                                                <div class="myJob-wrapper-single-balance-contents">
                                                    <div class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                        <h4 class="contract_single__balance-price">{{ $total_jobs ?? 0 }}</h4>
                                                    </div>
                                                    <p class="myJob-wrapper-single-balance-para">{{ __('Total Jobs') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-xxl-3 col-lg-6 col-sm-6 col-md-4">
                                            <div class="myJob-wrapper-single-balance">
                                                <div class="myJob-wrapper-single-balance-contents">
                                                    <div class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                        <h4 class="contract_single__balance-price">{{ $complete_order ?? 0 }}</h4>
                                                    </div>
                                                    <p class="myJob-wrapper-single-balance-para">{{ __('Complete Order') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-lg-6 col-sm-6 col-md-4">
                                            <div class="myJob-wrapper-single-balance">
                                                <div class="myJob-wrapper-single-balance-contents">
                                                    <div class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                        <h4 class="contract_single__balance-price">{{ $active_order ?? 0 }}</h4>
                                                    </div>
                                                    <p class="myJob-wrapper-single-balance-para">{{ __('Active Order') }}</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            {{--my projects--}}
                            <div class="single-profile-settings mt-3">
                                <div class="single-profile-settings-header">
                                    <div class="single-profile-settings-header-flex pb-2">
                                        <x-form.form-title :title="__('Latest Orders')" :class="'single-profile-settings-header-title'" />
                                        <a href="{{ route('client.order.all') }}" class="btn-profile btn-bg-1"> {{ __('All Orders') }} </a>
                                    </div>
                                    <x-notice.general-notice :description="__('Notice: The admin has the ability to update the payment status for transactions that are pending.')" />
                                </div>
                                <div class="single-profile-settings-inner profile-border-top">
                                    <div class="custom_table style-04">
                                        <table>
                                            <thead>
                                            <tr>
                                                <th>{{ __('Budget') }}</th>
                                                <th>{{ __('Delivery Time') }}</th>
                                                <th>{{ __('Payment Status') }}</th>
                                                <th>{{ __('Create Date') }}</th>
                                                <th>{{ __('Order Details') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($latest_orders as $order)
                                                <tr>
                                                    <td>{{ float_amount_with_currency_symbol($order->price) ?? '' }}</td>
                                                    <td>{{ __($order->delivery_time) ?? '' }}</td>
                                                    <td>
                                                        @if($order->payment_gateway != 'manual_payment' && $order->payment_status == 'pending')
                                                            <span class="btn btn-danger btn-sm">{{ __('Payment Failed') }}</span>
                                                        @elseif($order->payment_status == 'pending')
                                                            <span class="btn btn-warning btn-sm">{{ ucfirst(__($order->payment_status)) }}</span>
                                                        @else
                                                            <span class="btn btn-success btn-sm">{{ ucfirst(__($order->payment_status)) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $order->created_at->toFormattedDateString() }}</td>
                                                    <td><a href="{{ route('client.order.details',$order->id) }}" class="btn-profile btn-bg-1">{{ __('Order Details') }}</a></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{--my projects--}}
                            @if(get_static_option('job_enable_disable') != 'disable')
                                <div class="single-profile-settings mt-4">
                                    <div class="single-profile-settings-header">
                                        <div class="single-profile-settings-header-flex">
                                            <x-form.form-title :title="__('Latest Jobs')" :class="'single-profile-settings-header-title'" />
                                            <a href="{{ route('client.job.all') }}" class="btn-profile btn-bg-1"> {{ __('All Jobs') }} </a>
                                        </div>
                                    </div>
                                    <div class="single-profile-settings-inner profile-border-top">
                                        <div class="custom_table style-04">
                                            <table>
                                                <thead>
                                                <tr>
                                                    <th>{{ __('Title') }}</th>
                                                    <th>{{ __('Action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($my_jobs as $job)
                                                    <tr>
                                                        <td>{{ $job->title }}</td>
                                                        <td>
                                                            <a href="{{ route('client.job.edit',$job->id) }}" class="btn-profile btn-bg-1 edit_info_show_hide"> {{ __('Edit Job') }} </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{--order complete latest talents--}}   
                            @if($talents->count())
                            <div class="shop-contents-wrapper-right mt-4 mb-3">
                                <div class="col-lg-12">
                                    <div class="categoryWrap-wrapper-item">
                                        {{-- Custom arrows --}}
                                        <div class="append-project append_arrows_3315622888 mb-3 d-flex justify-content-end gap-2">
                                            <div class="prev-icon slick-arrow">
                                                <i class="fa-solid fa-arrow-left"></i>
                                            </div>
                                            <div class="next-icon slick-arrow">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </div>
                                        </div>
                                        {{-- Slider Wrapper --}}
                                        <div class="talent-slider">
                                            @foreach ($talents as $talent)
                                                <div class="talent-item px-2">
                                                    <div class="single-freelancer text-center radius-20 p-3 shadow-sm border position-relative mt-3 mb-3">
                                                        @if($talent->promotionalProfiles->count() > 0)
                                                            <div class="single-project-content-review pro-profile-badge">
                                                                <div class="pro-icon-background">
                                                                    <i class="fas fa-check"></i>
                                                                </div>
                                                                <small>{{ __('Pro') }}</small>
                                                            </div>
                                                        @endif
                                                        <div class="single-freelancer-author">
                                                            {{-- Profile image --}}
                                                           <a href="{{ route('freelancer.profile.details', $talent->username) }}" 
                                                                class="d-inline-block mb-2 position-relative" 
                                                                style="width: 100px; height: 100px;">

                                                                @if ($talent->image)
                                                                    <img src="{{ asset('assets/uploads/profile/' . $talent->image) }}"
                                                                        alt="{{ $talent->first_name }}"
                                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                                                                        onerror="this.onerror=null;this.src='{{ asset('assets/static/img/author/author.jpg') }}';">
                                                                @else
                                                                    <img src="{{ asset('assets/static/img/author/author.jpg') }}"
                                                                        alt="Author"
                                                                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                                @endif

                                                                {{-- Online/Offline Dot --}}
                                                                <div class="profile-status-icon" style="
                                                                    position: absolute;
                                                                    bottom: 6px;
                                                                    right: 77px;
                                                                    top: 8px;
                                                                    width: 16px;
                                                                    height: 16px;
                                                                    border-radius: 50%;
                                                                    background-color: {{ Cache::has('user_is_online_' . $talent->id) ? '#28a745' : '#6c757d' }};
                                                                    border: 2px solid #fff;">
                                                                </div>
                                                            </a>

                                                            {{-- Name --}}
                                                            <h4 class="single-freelancer-author-name mx-auto text-center">
                                                                <a href="{{ route('freelancer.profile.details', $talent->username) }}">
                                                                    {{ $talent->full_name }}
                                                                    @if($talent->user_verified_status == 1) 
                                                                        <i class="fas fa-circle-check" style="color:#309400; font-size: 16px;"></i>
                                                                    @endif
                                                                </a>
                                                            </h4>

                                                            {{-- Title --}}
                                                            <span class="single-freelancer-author-para d-block">
                                                                {{ $talent?->user_introduction?->title ?? '' }}
                                                            </span>

                                                            {{-- Jobs & Rate --}}
                                                            <p class="text-muted small mt-1 mb-1">
                                                                <i class="fas fa-briefcase"></i> {{ $talent->completed_jobs ?? 0 }} Jobs Completed
                                                            </p>
                                                            <p class="fw-bold" style="color: #309400;">
                                                                ${{ number_format($talent->hourly_rate, 2) }}/hr
                                                            </p>

                                                            {{-- Skills --}}
                                                            <div class="skills-list mt-2 text-center d-flex flex-wrap justify-content-center gap-2">
                                                                @php
                                                                    $skills = [];
                                                                    if ($talent->freelancer_skill->count()) {
                                                                        foreach ($talent->freelancer_skill as $skillObj) {
                                                                            $skills = array_merge($skills, explode(',', $skillObj->skill));
                                                                        }
                                                                    }
                                                                @endphp

                                                                @if(count($skills))
                                                                    @foreach($skills as $skill)
                                                                        <span class="badge bg-light text-dark border px-2 py-1">
                                                                            {{ trim($skill) }}
                                                                        </span>
                                                                    @endforeach
                                                                @else
                                                                    <span class="text-muted small">No skills added</span>
                                                                @endif
                                                            </div>

                                                            {{-- Rating --}}
                                                            <div class="mt-2">
                                                                {!! freelancer_rating_for_profile_details_page($talent->id) !!}
                                                            </div>
                                                        </div>

                                                        <div class="btn-wrapper">
                                                            <button 
                                                                class="cmn-btn btn-bg-gray btn-small radius-5 invite-btn mt-3" 
                                                                data-id="{{ $talent->id }}" 
                                                                data-name="{{ $talent->full_name }}" 
                                                                data-image="{{ $talent->image ? asset('assets/uploads/profile/'.$talent->image) : asset('assets/static/img/author/author.jpg') }}"
                                                                data-title="{{ $talent?->user_introduction?->title ?? '' }}"
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#inviteModal">
                                                                    {{ $talent->freelancer_orders->isNotEmpty() ? __('Re-Hire') : __('Invite to Job') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div> 
                                    </div>
                                </div>
                            </div>

                            @endif

                            <!-- Invite Modal -->
                            <div class="modal fade" id="inviteModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog ">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ __('Send Job Invitation') }}</h5>
                                            <button type="button" class="popup-contents-close popup-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                                        </div>
                                        <form id="inviteForm" method="POST" action="{{ route('client.send.invitation') }}">
                                            @csrf
                                            <input type="hidden" name="freelancer_id" id="freelancer_id">

                                            <div class="modal-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <img id="freelancer_image" src="" class="rounded-circle me-3" width="70" height="70" style="object-fit:cover;">
                                                    <div>
                                                    <h6 id="freelancer_name" class="mb-0"></h6>
                                                    <small id="freelancer_title" class="text-muted"></small>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Choose Job') }}</label>
                                                    <select name="job_id" class="form-control" required>
                                                    <option value="">{{ __('Select a Job') }}</option>
                                                    @foreach($my_jobs as $job)
                                                        <option value="{{ $job->id }}">{{ $job->title }}</option>
                                                    @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Message') }}</label>
                                                    <textarea name="message" class="form-control" rows="5">Hi, I'm {{ $clientName }}.., I'd like to invite you to take a look at the job I've posted. Please submit a proposal if you're available and interested.</textarea>
                                                    
                                                </div>
                                            </div>

                                            <div class="popup-contents-btn flex-btn profile-border-top justify-content-end gap-2 pe-3 pb-3">
                                                <x-btn.close 
                                                    :title="__('Cancel')" 
                                                    class="btn-profile btn-outline-gray btn-hover-danger color-one popup-close" 
                                                    data-bs-dismiss="modal" 
                                                    aria-label="Close" 
                                                />
                                                <x-btn.submit :title="__('Send Invitation')" :class="'btn-profile btn-bg-1'" />
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Profile Settings area end -->
    </main>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.talent-slider').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: true,
        arrows: true,
        prevArrow: $('.append_arrows_3315622888 .prev-icon'),
        nextArrow: $('.append_arrows_3315622888 .next-icon'),
        responsive: [
            { breakpoint: 1200, settings: { slidesToShow: 2 }},
            { breakpoint: 768,  settings: { slidesToShow: 1 }}
        ]
    });
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".invite-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("freelancer_id").value = this.dataset.id;
                document.getElementById("freelancer_name").innerText = this.dataset.name;
                document.getElementById("freelancer_title").innerText = this.dataset.title;
                document.getElementById("freelancer_image").src = this.dataset.image;

            });
        });
    });
</script>

<script>
    $(document).on('click', '.popup-close', function() {
        $(this).closest('.modal').modal('hide');
    });
</script>

<script>
    document.querySelectorAll('.single-freelancer').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const midX = rect.width / 2;
            const midY = rect.height / 2;

            const rotateX = ((y - midY) / midY) * 10; 
            const rotateY = ((x - midX) / midX) * -10;

            card.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.03)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'rotateX(0deg) rotateY(0deg) scale(1)';
            card.style.transition = 'transform 0.3s ease';
        });

        card.addEventListener('mouseenter', () => {
            card.style.transition = 'transform 0.1s ease';
        });
    });
</script>