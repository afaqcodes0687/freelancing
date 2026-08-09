<div class="row g-4">
    @php $current_date = \Carbon\Carbon::now()->toDateTimeString() @endphp
    @foreach($projects as $project)
        <div class="col-xxl-6">
            <div class="project-category-item radius-10">
                <div class="single-project project-catalogue">
                    <div class="single-project-thumb">
                        @php
                            $raw = $project->image;
                            $files = is_array($raw)
                                ? $raw
                                : (json_decode($raw) !== null && json_last_error() === JSON_ERROR_NONE
                                    ? json_decode($raw, true)
                                    : [$raw]);
                            $isCloud = cloudStorageExist() && in_array(Storage::getDefaultDriver(), ['s3', 'cloudFlareR2', 'wasabi']);
                        @endphp

                        @if(!empty($files))
                            @foreach($files as $index => $file)
                                @php
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    $fileUrl = $isCloud
                                        ? render_frontend_cloud_image_if_module_exists('project/' . $file, load_from: $project->load_from)
                                        : asset('assets/uploads/project/' . $file);
                                    if ($index > 0) break;
                                @endphp

                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg']))
                                    <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}">
                                        <img src="{{ $fileUrl }}" alt="{{ $project->title ?? '' }}" style="width: 100%; height: 240px; object-fit: cover;">
                                    </a>
                                @elseif(in_array($ext, ['mp4', 'mov', 'webm', 'mkv']))
                                    <video controls style="width: 100%; height: 240px; object-fit: cover;">
                                        <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
                                    </video>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank" class="d-block text-center p-3 border rounded bg-light">
                                        @if($ext == 'pdf')
                                            <img src="{{ asset('assets/icons/pdf-icon.png') }}" alt="PDF" style="width: 60px;">
                                        @elseif(in_array($ext, ['doc', 'docx']))
                                            <img src="{{ asset('assets/icons/word-icon.png') }}" alt="Word" style="width: 60px;">
                                        @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                            <img src="{{ asset('assets/icons/excel-icon.png') }}" alt="Excel" style="width: 60px;">
                                        @else
                                            <i class="fa fa-file text-muted fa-2x"></i><br>
                                            <small>{{ $file }}</small>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        @else
                            <img src="{{ asset('assets/uploads/project/project-default-logo.jpeg') }}" alt="default" style="width: 100%; height: 240px; object-fit: cover;">
                        @endif
                    </div>
                    <div class="single-project-content">
                        <div class="single-project-content-top align-items-center flex-between">
                            @if(moduleExists('PromoteFreelancer'))
                                @if($project->pro_expire_date >= $current_date  && $project->is_pro === 'yes')
                                    @if($is_pro == 1)
                                        {{--set is_pro value in session and get from project details controller for click count--}}
                                        @php Session::put('is_pro',$is_pro) @endphp
                                        <div class="single-project-content-review pro-profile-badge">
                                            <div class="pro-icon-background">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <small>{{ __('Pro') }}</small>
                                        </div>
                                    @endif
                                @endif
                            @endif
                            {!! project_rating($project->id) !!}
                        </div>
                        <h4 class="single-project-content-title">
                            <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}"> {{ $project->title }} </a>
                        </h4>
                    </div>
                    <div class="single-project-bottom flex-between">
                        <span class="single-project-content-price">
                            @if($project->basic_discount_charge)
                                {{ float_amount_with_currency_symbol($project->basic_discount_charge) }}
                                <s>{{ float_amount_with_currency_symbol($project->basic_regular_charge) }}</s>
                            @else
                                {{ float_amount_with_currency_symbol($project->basic_regular_charge) }}
                            @endif
                        </span>
                        <div class="single-project-delivery">
                            <span class="single-project-delivery-icon"> <i class="fa-regular fa-clock"></i>{{ __('Delivery') }}</span>
                            <span class="single-project-delivery-days"> {{ $project->basic_delivery }} </span>
                        </div>
                    </div>
                    <div class="project-category-item-bottom profile-border-top">
                        <div class="project-category-item-bottom-flex flex-between align-items-center">
                            <div class="project-category-right-flex flex-btn">
                                <x-frontend.bookmark :identity="$project->id" :type="'project'" />
                            </div>
                            <div class="project-category-item-btn flex-btn">
                                @if(moduleExists('SecurityManage'))
                                    @if(Auth::guard('web')->check() && Auth::guard('web')->user()->freeze_order_create == 'freeze')
                                        <a href="#" class="btn-profile btn-outline-1 @if(Auth::guard('web')->user()->freeze_order_create == 'freeze') disabled-link @endif"> {{ __('Order Now') }} </a>
                                    @else
                                        <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}" class="btn-profile btn-outline-1"> {{ __('Order Now') }} </a>
                                    @endif
                                @else
                                    <a href="{{ route('project.details', ['username' => $project->project_creator?->username, 'slug' => $project->slug]) }}" class="btn-profile btn-outline-1"> {{ __('Order Now') }} </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<x-pagination.laravel-paginate :allData="$projects"/>
