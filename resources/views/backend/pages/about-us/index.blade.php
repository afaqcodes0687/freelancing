@extends('backend.layout.master')
@section('site_title', __('About Us Management'))

@section('content')
    <div class="dashboard__content">
        <div class="dashboard__content__header">
            <h4 class="dashboard__content__title">{{ __('About Us Management') }}</h4>
            <div class="dashboard__content__header__right">
                @if($aboutUs)
                    <a href="{{ route('admin.about-us.edit', $aboutUs->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                @else
                    <a href="{{ route('admin.about-us.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Create') }}
                    </a>
                @endif
            </div>
        </div>

        @if($aboutUs)
            <div class="dashboard__card">
                <div class="dashboard__card__body">
                    <!-- CEO Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-left">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-user-tie mr-2"></i>{{ __('CEO Information') }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <p class="mb-1"><strong>{{ __('Name') }}:</strong> {{ $aboutUs->ceo_name }}</p>
                                            <p class="mb-1"><strong>{{ __('Title') }}:</strong> {{ $aboutUs->ceo_title }}</p>
                                            <p class="mb-0">{{ __('Description') }}:</p>
                                            <p class="text-muted small">{!! Str::limit(strip_tags($aboutUs->ceo_description), 150) !!}</p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            @if($aboutUs->ceo_image)
                                                <img src="{{ asset('assets/frontend/img/' . $aboutUs->ceo_image) }}" 
                                                     alt="{{ $aboutUs->ceo_name }}" 
                                                     class="img-fluid rounded-circle shadow" 
                                                     style="max-width: 120px; border: 3px solid #007bff;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-left border-success">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle mr-2"></i>{{ __('Main Content') }}
                                    </h5>
                                    <p class="mb-1"><strong>{{ __('Title') }}:</strong> {{ $aboutUs->main_title }}</p>
                                    <p class="mb-0 text-muted small">{!! Str::limit(strip_tags($aboutUs->main_description), 200) !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-left border-info">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-bar mr-2"></i>{{ __('Statistics') }}
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-2 text-center">
                                            <div class="stat-box">
                                                <h4 class="text-primary">{{ $aboutUs->clients_count }}</h4>
                                                <small>{{ __('Clients') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="stat-box">
                                                <h4 class="text-success">{{ $aboutUs->freelancers_count }}</h4>
                                                <small>{{ __('Freelancers') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="stat-box">
                                                <h4 class="text-info">{{ $aboutUs->orders_count }}</h4>
                                                <small>{{ __('Orders') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="stat-box">
                                                <h4 class="text-warning">{{ $aboutUs->jobs_handled }}</h4>
                                                <small>{{ __('Jobs') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="stat-box">
                                                <h4 class="text-danger">{{ $aboutUs->earned_amount }}</h4>
                                                <small>{{ __('Earned') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <div class="stat-box">
                                                <h4 class="text-secondary">{{ $aboutUs->awards_count }}</h4>
                                                <small>{{ __('Awards') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Members -->
                    @if($aboutUs->team_members)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-left border-warning">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-users mr-2"></i>{{ __('Team Members') }}
                                        </h5>
                                        <div class="row">
                                            @foreach(json_decode($aboutUs->team_members, true) as $member)
                                                <div class="col-md-2 text-center mb-3">
                                                    <div class="team-member-card">
                                                        @if($member['image'])
                                                            <img src="{{ asset('assets/frontend/img/' . $member['image']) }}" 
                                                                 class="img-fluid rounded-circle shadow mb-2" 
                                                                 style="max-width: 80px; border: 2px solid #ffc107;">
                                                        @endif
                                                        <h6 class="mb-1">{{ $member['name'] }}</h6>
                                                        <small class="text-muted">{{ $member['position'] }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Certifications -->
                    @if($aboutUs->certifications)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-left border-danger">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <i class="fas fa-award mr-2"></i>{{ __('Certifications') }}
                                        </h5>
                                        <div class="row">
                                            @foreach(json_decode($aboutUs->certifications, true) as $cert)
                                                <div class="col-md-3 text-center mb-3">
                                                    <div class="certification-card">
                                                        @if($cert['image'])
                                                            <img src="{{ asset('assets/frontend/img/' . $cert['image']) }}" 
                                                                 class="img-fluid rounded shadow mb-2" 
                                                                 style="max-width: 80px;">
                                                        @endif
                                                        <h6 class="mb-1">{{ $cert['title'] }}</h6>
                                                        @if($cert['link'])
                                                            <a href="{{ $cert['link'] }}" target="_blank" class="btn btn-primary">
                                                                <i class="fas fa-external-link-alt"></i> {{ __('View') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <a href="{{ route('admin.about-us.edit', $aboutUs->id) }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-edit"></i> {{ __('Edit About Us Page') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="dashboard__card">
                <div class="dashboard__card__body text-center py-5">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('No About Us Content Found') }}</h4>
                    <p class="text-muted">{{ __('Please create About Us content to display it here.') }}</p>
                    <a href="{{ route('admin.about-us.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Create About Us Page') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <style>
        .border-left {
            border-left: 4px solid;
            background: linear-gradient(to right, rgba(0,0,0,0.03) 0%, rgba(0,0,0,0) 100%);
        }
        .border-primary { border-left-color: #007bff; }
        .border-success { border-left-color: #28a745; }
        .border-info { border-left-color: #17a2b8; }
        .border-warning { border-left-color: #ffc107; }
        .border-danger { border-left-color: #dc3545; }
        
        .stat-box {
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-box:hover {
            transform: translateY(-2px);
        }
        
        .team-member-card, .certification-card {
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            height: 100%;
        }
        .team-member-card:hover, .certification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
    </style>
@endsection
