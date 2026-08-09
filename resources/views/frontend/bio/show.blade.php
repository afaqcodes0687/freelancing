@extends('frontend.layout.master')

@section('title', $user->fullname . ' - Bio')

@section('content')
<div class="bio-page {{ $user->bio_theme ?? 'default' }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <!-- Profile Section -->
                <div class="bio-profile-card text-center">
                    <!-- Avatar -->
                    <div class="bio-avatar">
                        @if($user->bio_avatar)
                            <img src="{{ $user->bio_avatar }}" alt="{{ $user->fullname }}" class="img-fluid rounded-circle">
                        @else
                            <div class="default-avatar">
                                {{ substr($user->fullname, 0, 2) }}
                            </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <h2 class="bio-name">{{ $user->fullname }}</h2>
                    <p class="bio-username">@{{ $user->username }}</p>
                    
                    @if($user->bio_description)
                        <p class="bio-description">{{ $user->bio_description }}</p>
                    @endif

                    <!-- Stats -->
                    <div class="bio-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $user->bio_views ?? 0 }}</span>
                            <span class="stat-label">Views</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $user->total_link_clicks ?? 0 }}</span>
                            <span class="stat-label">Clicks</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $user->activeBioLinks()->count() }}</span>
                            <span class="stat-label">Links</span>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="bio-qr-code">
                        <img src="{{ $qrCodeUrl }}" alt="QR Code" class="img-fluid">
                        <div class="qr-actions">
                            <a href="{{ route('bio.qr.download', [$user->username, 'png']) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> PNG
                            </a>
                            <a href="{{ route('bio.qr.download', [$user->username, 'svg']) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download"></i> SVG
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Featured Links -->
                @if($featuredLinks->count() > 0)
                    <div class="bio-section">
                        <h3 class="section-title">Featured</h3>
                        @foreach($featuredLinks as $link)
                            <a href="{{ route('bio.link.redirect', [$user->username, $link->id]) }}" 
                               class="bio-link featured-link"
                               @if($link->color) style="background-color: {{ $link->color }}; border-color: {{ $link->color }};" @endif>
                                @if($link->icon)
                                    <i class="{{ $link->icon }} link-icon"></i>
                                @endif
                                <span class="link-title">{{ $link->title }}</span>
                                <i class="fas fa-arrow-right link-arrow"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Regular Links -->
                @if($regularLinks->count() > 0)
                    <div class="bio-section">
                        <h3 class="section-title">Links</h3>
                        @foreach($regularLinks as $link)
                            <a href="{{ route('bio.link.redirect', [$user->username, $link->id]) }}" 
                               class="bio-link"
                               @if($link->color) style="background-color: {{ $link->color }}; border-color: {{ $link->color }};" @endif>
                                @if($link->icon)
                                    <i class="{{ $link->icon }} link-icon"></i>
                                @endif
                                <span class="link-title">{{ $link->title }}</span>
                                <i class="fas fa-arrow-right link-arrow"></i>
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- Services/Projects -->
                @if($projects->count() > 0)
                    <div class="bio-section">
                        <h3 class="section-title">Services</h3>
                        <div class="bio-projects">
                            @foreach($projects as $project)
                                <div class="bio-project-card">
                                    @if($project->image)
                                        <img src="{{ asset($project->image) }}" alt="{{ $project->title }}" class="project-image">
                                    @endif
                                    <div class="project-content">
                                        <h4 class="project-title">{{ $project->title }}</h4>
                                        <p class="project-price">
                                            @if($project->basic_discount_charge)
                                                {{ render_price($project->basic_discount_charge) }}
                                            @else
                                                {{ render_price($project->basic_regular_charge) }}
                                            @endif
                                        </p>
                                        <a href="{{ route('frontend.project.details', ['slug' => $project->slug]) }}" 
                                           class="btn btn-sm btn-primary">View Details</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($user->projects()->where('project_on_off', 1)->where('status', 1)->count() > 6)
                            <div class="text-center mt-3">
                                <a href="{{ route('frontend.freelancer.profile', ['username' => $user->username]) }}" 
                                   class="btn btn-outline-primary">View All Services</a>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Footer -->
                <div class="bio-footer text-center">
                    <p class="mb-0">
                        <small>
                            Powered by 
                            <a href="{{ url('/') }}" class="text-decoration-none">{{ config('app.name', 'RightFreelancer') }}</a>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Bio Page Styles */
.bio-page {
    min-height: 100vh;
    padding: 2rem 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bio-page.default {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bio-page.dark {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
}

.bio-page.minimal {
    background: #ffffff;
}

.bio-page.colorful {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.bio-profile-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

.bio-page.dark .bio-profile-card {
    background: rgba(0, 0, 0, 0.8);
    color: white;
}

.bio-page.minimal .bio-profile-card {
    background: white;
    border: 1px solid #e0e0e0;
}

.bio-avatar {
    margin-bottom: 1.5rem;
}

.bio-avatar img,
.default-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.default-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 auto;
}

.bio-name {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #333;
}

.bio-page.dark .bio-name {
    color: white;
}

.bio-username {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}

.bio-page.dark .bio-username {
    color: #ccc;
}

.bio-description {
    color: #555;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.bio-page.dark .bio-description {
    color: #ddd;
}

.bio-stats {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
}

.bio-page.dark .stat-number {
    color: #764ba2;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.bio-page.dark .stat-label {
    color: #ccc;
}

.bio-qr-code {
    margin-bottom: 1.5rem;
}

.bio-qr-code img {
    width: 150px;
    height: 150px;
    border-radius: 10px;
    margin-bottom: 1rem;
}

.qr-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.bio-section {
    margin-bottom: 2rem;
}

.section-title {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1rem;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.bio-page.minimal .section-title {
    color: #333;
}

.bio-link {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    margin-bottom: 0.75rem;
    background: white;
    border: 2px solid transparent;
    border-radius: 15px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.bio-page.dark .bio-link {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-color: rgba(255, 255, 255, 0.2);
}

.bio-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.featured-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-size: 1.1rem;
}

.link-icon {
    margin-right: 1rem;
    font-size: 1.2rem;
}

.link-title {
    flex: 1;
}

.link-arrow {
    margin-left: 1rem;
    transition: transform 0.3s ease;
}

.bio-link:hover .link-arrow {
    transform: translateX(5px);
}

.bio-projects {
    display: grid;
    gap: 1rem;
}

.bio-project-card {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    display: flex;
    gap: 1rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.bio-page.dark .bio-project-card {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.bio-project-card:hover {
    transform: translateY(-2px);
}

.project-image {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
}

.project-content {
    flex: 1;
}

.project-title {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #333;
}

.bio-page.dark .project-title {
    color: white;
}

.project-price {
    color: #667eea;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.bio-footer {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
    color: rgba(255, 255, 255, 0.7);
}

.bio-page.minimal .bio-footer {
    border-top-color: #e0e0e0;
    color: #666;
}

@media (max-width: 768px) {
    .bio-page {
        padding: 1rem 0;
    }
    
    .bio-profile-card {
        margin: 0 1rem 2rem;
        padding: 1.5rem;
    }
    
    .bio-stats {
        gap: 1rem;
    }
    
    .bio-link {
        padding: 0.75rem 1rem;
    }
}
</style>
@endsection
