<div class="tab-content-item active mt-5" id="proposals">
    <div class="myJob-wrapper">
        @if($job_details->job_proposals->count() > 0)
            @foreach($job_details->job_proposals as $proposal)
                <div class="myJob-wrapper-single">
                    {!! freelancer_skill_match_with_job_skill($proposal->freelancer_id, $job_details->id) !!}
                    <div class="myJob-wrapper-single-flex flex-between align-items-center">
                        <div class="myJob-wrapper-single-contents">
                            <div class="jobFilter-proposal-author-flex">
                                <div class="jobFilter-proposal-author-thumb position-relative" style="display: inline-block;">
                                    @if($proposal?->freelancer->image)
                                        <a href="{{ route('freelancer.profile.details', $proposal?->freelancer->username) }}">
                                            <img src="{{ asset('assets/uploads/profile/'.$proposal?->freelancer?->image) }}" alt="{{ $proposal?->freelancer?->fullname }}">
                                        </a>
                                    @else
                                        <a href="{{ route('freelancer.profile.details', $proposal?->freelancer->username) }}">
                                            <img src="{{ asset('assets/static/img/author/author.jpg') }}" alt="{{ __('AuthorImg') }}">
                                        </a>
                                    @endif

                                    {{-- Online/Offline Dot --}}
                                    <span class="profile-status-icon" style="
                                        position: absolute;
                                        bottom: 2px;
                                        right: 52px;
                                        top: 1px;
                                        width: 14px;
                                        height: 14px;
                                        border-radius: 50%;
                                        border: 2px solid #fff;
                                        background-color: {{ Cache::has('user_is_online_' . $proposal->freelancer->id) ? '#28a745' : '#6c757d' }};
                                    ">
                                    </span>
                                </div>

                                <div class="jobFilter-proposal-author-contents">
                                    <h4 class="jobFilter-proposal-author-contents-title">
                                        <a href="{{ route('freelancer.profile.details', $proposal?->freelancer->username) }}">
                                        {{ $proposal->freelancer?->fullname ?? '' }}
                                        </a>
                                        <!-- <x-status.user-active-inactive-check :userID="$proposal->freelancer->id" /> -->
                                    </h4>
                                    <p class="jobFilter-proposal-author-contents-subtitle mt-2">
                                        {{ $proposal->freelancer?->user_introduction?->title ?? '' }} · <span>{{ $proposal->freelancer?->user_state?->state ?? '' }}, {{ $proposal->freelancer?->user_country?->country ?? '' }}</span>
                                        @if($proposal->freelancer?->user_verified_status == 1)
                                            <span data-toggle="tooltip" title="{{ __('User Verified') }}">
                                                <i class="fas fa-circle-check" style="color:#1E90FF"></i>
                                            </span>
                                        @endif
                                    </p>
                                    <div class="jobFilter-proposal-author-contents-review mt-2">
                                        {!! freelancer_rating($proposal->freelancer_id) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="myJob-wrapper-single-arrow">
                            <div class="job-proposal-btn">
                                <div class="job-proposal-btn-item">
                                    <x-job.job-proposal-view :isView="$proposal->is_view" />
                                </div>
                                <div class="job-proposal-btn-item">
                                    <x-job.hire-short-list-check :isHired="$proposal->is_hired" :isShortListed="$proposal->is_short_listed" />
                                </div>
                                <div class="job-proposal-btn-item">
                                    @php
                                        \Carbon\Carbon::setLocale('en');
                                    @endphp

                                    <p class="jobFilter-proposal-author-contents-time">
                                        {{ $proposal->created_at->diffForHumans() }}
                                    </p>

                                    @php
                                        \Carbon\Carbon::setLocale(app()->getLocale());
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="jobFilter-proposal-offered profile-border-top">
                        <div class="jobFilter-proposal-offered-single">
                            <span class="offered">{{ __('Offered') }}
                                <span class="offered-price">{{ float_amount_with_currency_symbol($proposal->amount) }}</span>
                            </span>
                        </div>
                        <div class="jobFilter-proposal-offered-single">
                            <span class="offered">{{ __('Est. delivery duration') }} <span class="offered-days">{{ $proposal->duration }}</span> </span>
                        </div>
                        @if($job_details->type == 'hourly')
                        <div class="jobFilter-proposal-offered-single">
                            <span class="offered">{{ __(ucfirst($job_details->type)) }}
                             <span class="offered-price">{{ float_amount_with_currency_symbol($job_details->hourly_rate) }}</span>
                            </span>
                        </div>
                        @endif
                        @if($job_details->type == 'hourly')
                            <div class="jobFilter-proposal-offered-single">
                            <span class="offered">{{ __('Estimated hour') }}
                             <span class="offered-price">{{ $job_details->estimated_hours ?? '' }}</span>
                            </span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-between profile-border-top">
                        <div class="btn-wrapper rejected_interview_location_{{ $proposal->id }}">
                            <div class="btn-wrapper flex-btn gap-2">
                                @if($proposal->is_rejected == 1)
                                    <a href="javascript:void(0)" class="btn-profile btn-outline-gray">{{ __('Rejected') }}</a>
                                @else
                                    <a href="javascript:void(0)" class="btn-profile btn-outline-gray btn-hover-danger reject_proposal" data-proposal-id="{{ $proposal->id }}">{{ __('Reject') }}</a>
                                    <a href="javascript:void(0)"
                                       class="btn-profile btn-bg-1 click-interview take_freelancer_interview"
                                       data-job-id="{{ $job_details->id }}"
                                       data-proposal-id="{{ $proposal->id }}"
                                       data-freelancer-id="{{ $proposal->freelancer_id }}"
                                       data-job-title="{{ $job_details->title }}"
                                       data-job-level="{{ $job_details->level }}"
                                       data-job-type="{{ $job_details->type }}"
                                       data-job-create-date="{{ $job_details->created_at }}"
                                    >
                                        @if ($proposal->is_interview_take == 1) {{ __('Interviewed') }} @else {{ __('Take Interview') }} @endif
                                    </a>
                                @endif

                                @if($job_details->type == 'hourly')
                                    <a href="javascript:void(0)"
                                       data-bs-toggle="modal"
                                       data-bs-target="#RateAndHoursModal"
                                       class="btn-profile btn-bg-1">{{ __('Update Hourly Rate') }}</a>
                                    @endif
                            </div>
                        </div>
                        <div class="btn-wrapper flex-btn gap-2 add_remove_interview_location_{{$proposal->id}}">
                            @if($proposal->is_rejected == 0)
                                <a href="javascript:void(0)" class="btn-profile btn-outline-gray loadingRound add_remove_shortlist" data-proposal-id="{{ $proposal->id }}">
                                    @if($proposal->is_short_listed == 0)
                                        <span class="add_to_short_listed">{{ __('Add to Shortlist') }}</span>
                                    @else
                                        <span class="remove_from_short_listed">{{ __('Remove from Shortlist') }}</span>
                                    @endif
                                </a>
                            @endif
                            <a href="{{ route('client.job.proposal.details',$proposal->id) }}" target="_blank" class="btn-profile btn-bg-1">{{ __('View Proposal') }}</a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <h4 class="jobFilter-proposal-author-contents-title text-danger"> {{ __('Nothing Found') }} </h4>
        @endif

    </div>
</div>