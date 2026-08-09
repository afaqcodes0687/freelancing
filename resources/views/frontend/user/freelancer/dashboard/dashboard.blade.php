@extends('frontend.layout.master')
@section('site_title', __('Dashboard'))
@section('style')
    <style>
        .total_balance {
            background-color: #e3e1ff !important;
        }

        /* Slick Slider Flex Fix for Equal Height */
        .jobs-slider .slick-track {
            display: flex !important;
            align-items: stretch !important;
        }

        .jobs-slider .slick-slide {
            height: auto !important;
            display: flex !important;
        }

        .jobs-slider .talent-item {
            display: flex !important;
            height: 100% !important;
            width: 100%;
        }

        .jobs-slider .single-freelancer {
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            width: 100%;
            border-radius: 20px;
            padding: 1.5rem !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04), 0 8px 16px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #f0f0f0 !important;
            background: #fff;
        }

        .single-freelancer:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        /* Fixed height for author name */
        .single-freelancer-author-name {
            font-size: 18px;
            font-weight: 700;
            margin-top: 10px;
            height: 28px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            display: block;
            width: 100%;
        }

        /* Fixed height for job title (2 lines) */
        .single-freelancer-author-para {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 42px;
            line-height: 21px;
            margin-bottom: 5px;
            font-size: 14px;
            text-align: center;
            width: 100%;
        }

        /* Fixed height for description (3 lines) */
        .single-jobs-para {
            height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            margin-top: 15px !important;
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }

        /* Fixed height for skills list */
        .skills-list {
            height: 85px;
            overflow: hidden;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            align-content: flex-start;
            margin-top: 15px !important;
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

        .single-freelancer-author-para a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .single-freelancer-author-para a:hover {
            color: #309400;
        }

        .single-jobs-price {
            font-size: 20px;
            color: #309400;
            font-weight: 700;
        }

        .single-jobs-price-fixed {
            font-size: 12px;
            color: #666;
            font-weight: 400;
            margin-left: 4px;
        }

        .center-text {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <main>
        <x-breadcrumb.user-profile-breadcrumb :title="__('Dashboard')" :innerTitle="__('Dashboard')" />
        <div class="responsive-overlay"></div>
        <div class="profile-settings-area pat-100 pab-100 section-bg-2">
            <div class="container">
                <div class="row g-4">
                    @include('frontend.user.layout.partials.sidebar')
                    <div class="col-xl-9 col-lg-8">
                        <div class="profile-settings-wrapper">

                            {{-- Dashboard Info --}}
                            <div class="single-profile-settings">
                                <div class="single-profile-settings-header">
                                    <div class="single-profile-settings-header-flex">
                                        <x-form.form-title :title="__('Dashboard Info')"
                                            :class="'single-profile-settings-header-title'" />
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
                                                    <div
                                                        class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                        <h4 class="contract_single__balance-price">
                                                            {{ float_amount_with_currency_symbol($total_wallet_balance) ?? 0.0 }}
                                                        </h4>
                                                    </div>
                                                    <p class="myJob-wrapper-single-balance-para">{{ __('Wallet Balance') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        @if(get_static_option('project_enable_disable') != 'disable')
                                            <div class="col-xxl-3 col-lg-6 col-sm-6 col-md-4">
                                                <a
                                                    href="{{ route('freelancer.profile.details', Auth::guard('web')->user()->username) }}">
                                                    <div class="myJob-wrapper-single-balance">
                                                        <div class="myJob-wrapper-single-balance-contents">
                                                            <div
                                                                class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                                <h4 class="contract_single__balance-price">
                                                                    {{ $total_project ?? 0 }}
                                                                </h4>
                                                            </div>
                                                            <p class="myJob-wrapper-single-balance-para">
                                                                {{ __('Total Projects') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endif

                                        <div class="col-xxl-3 col-lg-6 col-sm-6 col-md-4">
                                            <div class="myJob-wrapper-single-balance">
                                                <div class="myJob-wrapper-single-balance-contents">
                                                    <div
                                                        class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                        <h4 class="contract_single__balance-price">
                                                            {{ $complete_order ?? 0 }}
                                                        </h4>
                                                    </div>
                                                    <p class="myJob-wrapper-single-balance-para">{{ __('Complete Order') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-lg-6 col-sm-6 col-md-4">
                                            <div class="myJob-wrapper-single-balance">
                                                <div class="myJob-wrapper-single-balance-contents">
                                                    <div
                                                        class="myJob-wrapper-single-balance-price d-flex gap-2 justify-content-between">
                                                        <h4 class="contract_single__balance-price">{{ $active_order ?? 0 }}
                                                        </h4>
                                                    </div>
                                                    <p class="myJob-wrapper-single-balance-para">{{ __('Active Order') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Latest Projects --}}
                            @if(get_static_option('project_enable_disable') != 'disable')
                                <div class="single-profile-settings mt-4">
                                    <div class="single-profile-settings-header">
                                        <div class="single-profile-settings-header-flex">
                                            <x-form.form-title :title="__('Latest Projects')"
                                                :class="'single-profile-settings-header-title'" />
                                            <a href="{{ route('freelancer.profile.details', Auth::guard('web')->user()->username) }}"
                                                class="btn-profile btn-bg-1"> {{ __('All Projects') }} </a>
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
                                                    @foreach($my_projects as $project)
                                                        <tr>
                                                            <td>{{ $project->title }}</td>
                                                            <td>
                                                                <a href="{{ route('freelancer.project.edit', $project->id) }}"
                                                                    class="btn-profile btn-bg-1 edit_info_show_hide">
                                                                    {{ __('Edit Project') }} </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Latest Orders --}}
                            <div class="single-profile-settings mt-4 mb-3">
                                <div class="single-profile-settings-header">
                                    <div class="single-profile-settings-header-flex">
                                        <x-form.form-title :title="__('Latest Orders')"
                                            :class="'single-profile-settings-header-title'" />
                                        <a href="{{ route('freelancer.order.all') }}" class="btn-profile btn-bg-1">
                                            {{ __('All Orders') }} </a>
                                    </div>
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
                                                        <td>{{ __($order->payment_status) ?? '' }}</td>
                                                        <td>{{ $order->created_at->toFormattedDateString() }}</td>
                                                        <td><a href="{{ route('freelancer.order.details', $order->id) }}"
                                                                class="btn-profile btn-bg-1">{{ __('Order Details') }}</a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Jobs Slider --}}
                            <div class="single-profile-settings mt-4">
                                <div class="append-project append_arrows_jobs mb-3 d-flex justify-content-end gap-2">
                                    <div class="prev-icon slick-arrow"><i class="fa-solid fa-arrow-left"></i></div>
                                    <div class="next-icon slick-arrow"><i class="fa-solid fa-arrow-right"></i></div>
                                </div>
                                <div class="talent-slider jobs-slider w-100">
                                    @foreach ($jobs as $job)
                                        <div class="talent-item px-2">
                                            <div
                                                class="single-freelancer radius-20 shadow-sm border position-relative mt-3 mb-3">
                                                <div class="card-upper-section">
                                                    <a href="{{ route('freelancer.profile.details', $job->job_creator->username ?? '') }}"
                                                        class="d-inline-block mb-2 position-relative mx-auto"
                                                        style="width: 100px; height: 100px; display: block !important;">

                                                        @if ($job->job_creator && $job->job_creator->image)
                                                            <img src="{{ asset('assets/uploads/profile/' . $job->job_creator->image) }}"
                                                                alt="{{ $job->job_creator->full_name ?? 'Client' }}"
                                                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"
                                                                onerror="this.onerror=null;this.src='{{ asset('assets/static/img/author/author.jpg') }}';">
                                                        @else
                                                            <img src="{{ asset('assets/static/img/author/author.jpg') }}"
                                                                alt="Client"
                                                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                                        @endif

                                                        {{-- Online/Offline Dot --}}
                                                        @if($job->job_creator)
                                                            <div class="profile-status-icon" style="
                                                                                    position: absolute;
                                                                                    top: 5px;
                                                                                    left: 5px;
                                                                                    width: 15px;
                                                                                    height: 15px;
                                                                                    border-radius: 50%;
                                                                                    background-color: {{ Cache::has('user_is_online_' . $job->job_creator->id) ? '#28a745' : '#6c757d' }};
                                                                                    border: 2px solid #fff;
                                                                                "></div>
                                                        @endif
                                                    </a>
                                                    <div class="single-freelancer-author">
                                                        <h4 class="single-freelancer-author-name mx-auto text-center">
                                                            <a
                                                                href="{{ route('freelancer.profile.details', $job->job_creator->username ?? '') }}">
                                                                {{ $job->job_creator->full_name ?? 'Client' }}
                                                            </a>
                                                        </h4>
                                                    </div>

                                                    {{-- Job Title --}}
                                                    <span class="single-freelancer-author-para mt-2">
                                                        @if($job->job_creator)
                                                            <a
                                                                href="{{ route('job.details', ['username' => $job->job_creator->username, 'slug' => $job->slug]) }}">
                                                                {{ $job->title }}
                                                            </a>
                                                        @else
                                                            <span>{{ $job->title }}</span>
                                                        @endif
                                                    </span>

                                                    <p class="single-jobs-date text-center">
                                                        {{ $job->created_at->toFormattedDateString() ?? '' }} -
                                                        <span
                                                            class="fw-bold">{{ ucfirst(__($job->level)) ?? '' }}</span>
                                                    </p>

                                                    <div
                                                        class="obFilter-wrapper-item-contents-flex d-flex justify-content-center">
                                                        {{-- Job Price --}}
                                                        @php $price = ($job->type == 'hourly') ? $job->hourly_rate : $job->budget; @endphp
                                                        <h3 class="single-jobs-price text-center">
                                                            {{ float_amount_with_currency_symbol($price) }}
                                                            <span
                                                                class="single-jobs-price-fixed">{{ ucfirst(__($job->type)) }}</span>
                                                        </h3>
                                                    </div>

                                                    <p class="single-jobs-para mt-3 text-center">
                                                        {!! Str::limit(strip_tags($job->description), 80) !!}
                                                    </p>

                                                    {{-- Skills --}}
                                                    <div class="skills-list mt-2">
                                                        @if($job->job_skills->count())
                                                            @foreach($job->job_skills as $skill)
                                                                <span class="badge">
                                                                    {{ $skill->skill }}
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted small">No skills added</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="card-bottom-section mt-auto">
                                                    @if($job->job_creator)
                                                        <div class="btn-wrapper">
                                                            <a href="{{ route('job.details', ['username' => $job->job_creator->username, 'slug' => $job->slug]) }}"
                                                                class="cmn-btn btn-bg-gray btn-small radius-5 mt-3 w-100">
                                                                {{ __('Send Proposal') }}
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="btn-wrapper">
                                                            <a href="javascript:void(0)"
                                                                class="cmn-btn btn-bg-gray btn-small radius-5 mt-3 w-100 disabled">
                                                                {{ __('Send Proposal') }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            $('.jobs-slider').slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: false,
                vertical: false,
                prevArrow: $('.append_arrows_jobs .prev-icon'),
                nextArrow: $('.append_arrows_jobs .next-icon'),
                responsive: [
                    { breakpoint: 1200, settings: { slidesToShow: 2 } },
                    { breakpoint: 768, settings: { slidesToShow: 1 } }
                ]
            });
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
@endsection