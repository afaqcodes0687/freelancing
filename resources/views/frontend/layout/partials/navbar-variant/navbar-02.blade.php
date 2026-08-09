<header class="header-style-01">
    <!-- Menu area Starts -->
    <nav class="navbar navbar-area navbar-expand-lg" @if(get_static_option('sticky_menu') == 'enable') id="navigation"
    @endif>
        <div class="container bg-white nav-container">
            <div class="logo-wrapper">
                <a href="{{ route('homepage') }}" class="logo">
                    @if(!empty(get_static_option('site_logo')))
                        <img src="{{ asset('assets/static/img/logo/logo.png') }}" alt="site-logo">
                    @else
                        <img src="{{ asset('assets/uploads/media-uploader/logo172632858617267305591731011557.png') }}"
                            alt="site-logo">
                    @endif
                </a>
            </div>
            <div class="responsive-mobile-menu d-lg-none">
                <button type="button" class="click-nav-right-icon" aria-label="More options">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#xilancer_menu" aria-label="Toggle navigation" aria-expanded="false" aria-controls="xilancer_menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="xilancer_menu">
                <ul class="navbar-nav">

                    <li class=" current-menu-item ">
                        <a href="/">Home</a>
                    </li>
                    <li>
                        <a href="{{ Auth::guard('web')->check() ? '/jobs' : route('user.login') }}">Jobs</a>
                    </li>
                    <li>
                        <a href="{{ Auth::guard('web')->check() ? '/talents' : route('user.login') }}">Talents</a>
                    </li>
                    <li>
                        <a
                            href="{{ Auth::guard('web')->check() ? route('subscriptions.all') : route('user.login') }}">Packages</a>
                    </li>
                    <li>
                        <a href="{{ Auth::guard('web')->check() ? '/projects' : route('user.login') }}">Projects</a>
                    </li>
                    <li>
                        <a href="/how-it-works">How It Works</a>
                    </li>
                </ul>
            </div>
            <div class="collapse navbar-collapse" id="xilancer_menu">
                <ul class="navbar-nav">
                    {!! render_frontend_menu($primary_menu) !!}
                </ul>
            </div>

            <x-frontend.user-menu />
        </div>
    </nav>
    @if(request()->routeIs('homepage'))
        <x-frontend.category.category />
    @endif
    <!-- Menu area end -->
</header>