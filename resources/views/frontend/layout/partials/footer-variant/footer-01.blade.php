{{--
<footer class="footer-area footer-fluid white-footer footer-bg-1">
    <div class="container">
        <div class="footer-area-wrapper footer-bg-1">
            <div class="row gx-5 footer-area-top">
                {!! render_frontend_sidebar('footer_one') !!}
            </div>

            <div class="copyright-area copyright-border">
                <div class="row">
                    <div class="col-12">
                        <div class="footer-widget-para">
                            {!! render_footer_copyright_text() !!}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</footer>
--}}


<footer class="footer-area footer-fluid white-footer footer-bg-1 footer-bg-add">
    <div class="container">
        <div class="footer-area-wrapper footer-bg-1">
            <div class="row gx-5 footer-area-top">
                <div class="col-lg-3 col-sm-6 mt-2">
                    <div class="footer-widget widget">
                        <div class="footer-contents-logo">
                            <a href="{{ url('/') }}" class="footer-contents-logo-img"><img
                                    src="{{ asset('assets/uploads/media-uploader/footer-logo.png') }}"
                                    alt="logo"></a>
                        </div>
                        <div class="footer-widget-inner mt-4">
                            <p class="footer-widget-para">Through right freelancer platform, clients can hire
                                freelancers to do work in areas such as graphic
                                designer, software development, writing, SEO, an..</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 mt-2">
                    <div class="footer-widget widget">
                        <h4 class="footer-widget-title">Talents</h4>
                        <div class="footer-widget-inner mt-4">
                            <ul class="footer-widget-link-list">
                                <li class="footer-widget-link-list-item">
                                    <a href="{{ Auth::guard('web')->check() ? '/jobs' : route('user.login') }}">Jobs</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{url('categories/website-development')}}">Categories</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{ Auth::guard('web')->check() ? '/projects' : route('user.login') }}">Projects
                                        (gigs)</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a
                                        href="{{ Auth::guard('web')->check() ? '/talents' : route('user.login') }}">Talents</a>
                                </li>
                               
                                <li class="footer-widget-link-list-item">
                                    <a
                                        href="{{ Auth::guard('web')->check() ? route('subscriptions.all') : route('user.login') }}">Packages</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{url('/contact-us')}}">Contact Us</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{url('blogs/all')}}">Blogs</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 mt-2">
                    <div class="footer-widget widget">
                        <h4 class="footer-widget-title">Company</h4>
                        <div class="footer-widget-inner mt-4">
                            <ul class="footer-widget-link-list">
                                <li class="footer-widget-link-list-item">
                                    <a href="{{route('about-us')}}">About Us</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{route('how-it-works')}}">How It Works</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{route('fees-and-charge')}}">Fees and Charges</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{route('privacy-policy')}}">Privacy Policy</a>
                                </li>
                                <li class="footer-widget-link-list-item">
                                    <a href="{{route('terms-of-service')}}">Terms of Service</a>
                                </li>

                                <li class="footer-widget-link-list-item">
                                    <a href="{{route('support')}}">Support</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-sm-6 mt-2">
                    <div class="footer-widget widget">
                        <h4 class="footer-widget-title">Explore More</h4>
                        <div class="footer-widget-inner mt-4">
                            <ul class="footer-widget-link-list">
                                <li class="footer-widget-link-list-item">
                                    <a href="{{ Auth::guard('web')->check() ? '/talents' : route('user.login') }}">Freelancers
                                        by Skill</a>
                                </li>
                            
                                <li class="footer-widget-link-list-item">
                                    <a href="{{ Auth::guard('web')->check() ? '/jobs' : route('user.login') }}">Find
                                        Jobs</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copyright-area copyright-border">
                <div class="row">
                    <div class="col-12">
                        <div class="footer-widget-para mt-1">
                            <p> &copy; {{ date('Y') }} All right reserved by <a href="{{url('/')}}"
                                    style="color: #309400;" target="_blank">RightFreelancer</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>