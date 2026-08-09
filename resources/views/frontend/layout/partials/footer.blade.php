@php
    $footer_variant = !is_null(get_footer_style()) ? get_footer_style() : '02';
@endphp
@include('frontend.layout.partials.footer-variant.footer-'.$footer_variant)

@if(get_static_option('bottom_to_top') != 'disable')
    <!-- back to top area start -->
    <div class="back-to-top">
        <span class="back-top"> <i class="fas fa-angle-up" style="margin-top:12px"></i> </span>
    </div>
    <!-- back to top area end -->
@endif

@if(get_static_option('mouse_pointer') != 'disable')
    <!-- Mouse Cursor start -->
    <div class="mouse-move mouse-outer"></div>
    <div class="mouse-move mouse-inner"></div>
    <!-- Mouse Cursor Ends -->
@endif

@php
    $isHomePage = request()->routeIs('homepage');
    $scriptType = $isHomePage ? 'text/lazy-js' : 'text/javascript';
    $srcAttr = $isHomePage ? 'data-src' : 'src';
@endphp

<!-- jquery -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/common/js/jquery-3.7.1.min.js') }}"></script>
<!-- jquery Migrate -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/common/js/jquery-migrate-3.4.0.min.js') }}"></script>
<!-- bootstrap -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/frontend/js/bootstrap.bundle.min.js') }}"></script>
@if(get_static_option('home_page_animation') != 'disable')
<!-- Wow Js -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/frontend/js/wow.js') }}"></script>
@endif
<!-- Slick Js -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/frontend/js/slick.js') }}"></script>
<!-- All Plugin Js -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/frontend/js/all_plugin.js') }}"></script>
<!-- Magnific popup Js -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/frontend/js/jquery.magnific-popup.js') }}"></script>
<!-- main js -->
<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/frontend/js/main.js') }}"></script>
<!-- Toastr js -->
@if(get_static_option('home_page_animation') != 'disable')
    <!-- Wow Js -->
    <script type="{{ $scriptType }}">new WOW().init();</script>
@endif

<script type="{{ $scriptType }}" {!! $srcAttr !!}="{{ asset('assets/common/js/toastr.min.js') }}"></script>
@php
    $toastrOutput = Toastr::message();
    if($isHomePage) {
        $toastrOutput = str_replace('<script>', '<script type="text/lazy-js">', $toastrOutput);
    }
@endphp
{!! $toastrOutput !!}
<script type="{{ $scriptType }}">
    @if(Session::has('msg') && Session::has('type'))
        @if(Session::get('type') == 'success')
            toastr.success("{{ Session::get('msg') }}");
        @elseif(Session::get('type') == 'warning')
            toastr.warning("{{ Session::get('msg') }}");
        @elseif(Session::get('type') == 'info')
            toastr.info("{{ Session::get('msg') }}");
        @else
            toastr.error("{{ Session::get('msg') }}");
        @endif
    @endif
</script>
<!-- global ajax setup -->
<script type="{{ $scriptType }}"> $.ajaxSetup({headers: {'X-CSRF-TOKEN': '{{csrf_token()}}'} }) </script>

@if(moduleExists('HourlyJob'))
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" defer></script>
@endif

<script type="{{ $scriptType }}">
    (function($){
        "use strict";
        $(document).ready(function(){
            $(document).on('mouseup', function (e) {
                if ($(e.target).closest('.navbar-right-notification').find('.navbar-right-notification-wrapper').length === 0) {
                    $('.navbar-right-notification-wrapper').removeClass('active');
                }
            });
            $(document).on('click', '.navbar-right-notification-icon', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $('.navbar-right-notification-wrapper').toggleClass('active');
                @php  $user_type =  Auth::guard('web')->check() && Auth::guard('web')->user()->user_type == 2 ? 'freelancer' : 'client'  @endphp
                $.ajax({
                    url:"{{ route($user_type.'.'.'notification.read') }}",
                    method:'POST',
                    success: function(res){
                        if(res.status == 'success'){
                            let status = res.status
                        }
                    }
                });
            });

            $(document).on('click', '.subscription_by_email', function(e){
                e.preventDefault();
                let email = $('#newsletter_subscribe_from_addon input[name="email"]').val();
                let erContainer = $("#newsletter_subscribe_from_addon .error-message");
                erContainer.html('');
                $.ajax({
                    url:"{{ route('newsletter.subscription')}}",
                    data:{email:email},
                    method:'POST',
                    error:function(res){
                        let errors = res.responseJSON;
                        erContainer.html('<div class="alert alert-danger text-start"></div>');
                        $.each(errors.errors, function(index,value){
                            erContainer.find('.alert.alert-danger').append('<p>'+value+'</p>');
                        });
                    },
                    success: function(res){
                        if(res.status=='success'){
                            toastr_success_js("{{ __('Thanks to Subscription Us.') }}")
                            $('input[name="email"]').val('')
                        }
                        if(res.status == 'failed'){
                            erContainer.html('<div class="alert alert-danger">'+res.msg+'</div>');
                        }
                    }

                });
            });
            $(document).on('click', '.subscription_by_email_newsletter', function(e){
                e.preventDefault();
                let email = $('#newsletter_subscribe_from_footer input[name="email"]').val();
                let erContainer = $("#newsletter_subscribe_from_footer .error-message");
                erContainer.html('');
                $.ajax({
                    url:"{{ route('newsletter.subscription')}}",
                    data:{email:email},
                    method:'POST',
                    error:function(res){
                        let errors = res.responseJSON;
                        erContainer.html('<div class="alert alert-danger text-start"></div>');
                        $.each(errors.errors, function(index,value){
                            erContainer.find('.alert.alert-danger').append('<p>'+value+'</p>');
                        });
                    },
                    success: function(res){
                        if(res.status=='success'){
                            toastr_success_js("{{ __('Thanks to Subscription Us.') }}")
                            $('input[name="email"]').val('')
                        }
                        if(res.status == 'failed'){
                            erContainer.html('<div class="alert alert-danger">'+res.msg+'</div>');
                        }
                    }

                });
            });

            //faq question
            $(document).on('click', '.ask_you_question', function(e){
                e.preventDefault();
                let question = $('input[name="question"]').val();
                let erContainer = $("#ask_your_question .error-message");
                erContainer.html('');
                $.ajax({
                    url:"{{ route('faq.question')}}",
                    data:{question:question},
                    method:'POST',
                    error:function(res){
                        let errors = res.responseJSON;
                        erContainer.html('<div class="alert alert-danger text-start"></div>');
                        $.each(errors.errors, function(index,value){
                            erContainer.find('.alert.alert-danger').append('<p>'+value+'</p>');
                        });
                    },
                    success: function(res){
                        if(res.status=='success'){
                            toastr_success_js("{{ __('Thanks to Question Us.') }}")
                            $('input[name="question"]').val('')
                            $("#questionModal").modal('hide');
                        }
                        if(res.status == 'failed'){
                            erContainer.html('<div class="alert alert-danger">'+res.msg+'</div>');
                        }
                    }

                });
            });

            //bookmarks
            $(document).on('click','.click_to_bookmark',function(){
                let identity = $(this).data('identity');
                let route = $(this).data('route');
                let type = $(this).data('type');
                let login = $(this).data('login') ?? '';
                if(login == 'login-please'){
                    toastr_warning_js("{{ __('Please login to bookmark.') }}")
                    return false
                }
                $.ajax({
                    url: route,
                    type: 'post',
                    data: {identity:identity, type:type},
                    success: function(res){
                        if(res.status == 'success'){
                            toastr_success_js("{{ __('Successfully bookmarked.') }}")
                            $(".bookmark_area").load(location.href + ' .bookmark_area');
                        }else{
                            toastr_warning_js("{{ __('Something went wrong.') }}");
                        }
                    }
                });
            });

            //bookmarks remove
            $(document).on('click','.remove_from_bookmark',function(){
                let identity = $(this).data('identity');
                let route = $(this).data('route');
                $.ajax({
                    url: route,
                    type: 'post',
                    data: {identity:identity},
                    success: function(res){
                        $('#current_password_match').show();
                        if(res.status == 'success'){
                            toastr_success_js("{{ __('Successfully remove from bookmarked.') }}")
                            $(".bookmark_area").load(location.href + ' .bookmark_area');
                        }else{
                            toastr_warning_js("{{ __('Something went wrong.') }}");
                        }
                    }
                });
            });


            //job search from home page
            $(document).on('keyup', '#search_your_desired_job',function(){
                let job_search_string = $('#search_your_desired_job').val();
                let search_type = $('#Select_project_or_job_for_search').val();
                $('.display_search_result').hide()

                if(job_search_string.length >= 1){
                    $('.display_search_result').show()
                    $('#header_search_load_spinner').html('<i class="fas fa-spinner fa-pulse"></i>');
                    $.ajax({
                        url:"{{route('home.job.project.search')}}",
                        method:"GET",
                        data:{job_search_string:job_search_string, search_type},
                        success:function(res){
                            $('.display_search_result').html(res);
                            $('#header_search_load_spinner').html('<i class="fas fa-search"></i>');
                        }
                    })
                }else{
                    $('.display_search_result').html('');
                }

            })

            $('.video_play').magnificPopup({
                type:'iframe',
            });


            //fixed menu js
            if ($('#navigation').length) {
                window.onscroll = function () { myFunction() };

                let navbar = document.getElementById("navigation");
                let sticky = navbar.offsetTop;

                function myFunction() {
                    if (window.pageYOffset >= sticky) {
                        navbar.classList.add("sticky")
                    }
                    if (window.pageYOffset == sticky) {
                        navbar.classList.remove("sticky");
                    }

                }
            }

        });
    }(jQuery));
</script>


<script type="{{ $scriptType }}">
    function slickSliderConfiguration() {
        let global = document.querySelectorAll('.global-slick-init');
        global.forEach(function (element, index){
            let parentBoxWidth = element.clientWidth;
            let childCount = element.querySelectorAll('.category-slider-item, .testimonial-item')?.length ?? 0;
            let childItemWidth = element.querySelector('.category-slider-item, .testimonial-item')?.clientWidth ?? 0;
            if(childCount !== 0 && childItemWidth !== 0){
                if((childCount * childItemWidth) < parentBoxWidth){
                    let targetSwipeDiv = element.parentElement.parentElement.parentElement.querySelector('.testimonial-arrows');
                    targetSwipeDiv.classList.add('d-none');
                    targetSwipeDiv.parentElement.classList.remove('mt-5')
                }
            }
        })
    }
    window.addEventListener('load', slickSliderConfiguration,false);
    window.addEventListener('resize', slickSliderConfiguration,false);
</script>

<script>
    //toastr warning
    function toastr_warning_js(msg){
        Command: toastr["warning"](msg, "Warning !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }

    //toastr success
    function toastr_success_js(msg){
        Command: toastr["success"](msg, "Success !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }

    //toastr error
    function toastr_error_js(msg){
        Command: toastr["error"](msg, "Error !")
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    }
</script>

@if(moduleExists('HourlyJob'))
    <script>
        {{--document.getElementById('capture').addEventListener('click', async () => {--}}
        {{--    try {--}}
        {{--        // Prompt user to select a screen or window--}}
        {{--        const stream = await navigator.mediaDevices.getDisplayMedia({--}}
        {{--            video: {--}}
        {{--                cursor: "always"--}}
        {{--            }--}}
        {{--        });--}}

        {{--        // Get the video track from the stream--}}
        {{--        const track = stream.getVideoTracks()[0];--}}
        {{--        // Create a video element to play the stream--}}
        {{--        const video = document.createElement('video');--}}
        {{--        video.srcObject = stream;--}}
        {{--        video.onloadedmetadata = () => {--}}
        {{--            video.play();--}}
        {{--            // Create a canvas to capture the frame--}}
        {{--            const canvas = document.getElementById('canvas');--}}
        {{--            canvas.width = video.videoWidth;--}}
        {{--            canvas.height = video.videoHeight;--}}
        {{--            // Draw the video frame onto the canvas--}}
        {{--            const context = canvas.getContext('2d');--}}
        {{--            context.drawImage(video, 0, 0, canvas.width, canvas.height);--}}
        {{--            // Convert the canvas to an image (base64 format)--}}
        {{--            const screenshotData = canvas.toDataURL('image/png');--}}
        {{--            const order_id_for_screenshot = $('#order_id_for_screenshot').val();--}}

        {{--            // Send the screenshot data to the server--}}
        {{--            sendScreenshotToServer(screenshotData,order_id_for_screenshot);--}}
        {{--            // Stop the screen capture--}}
        {{--            track.stop();--}}
        {{--        };--}}
        {{--    }--}}
        {{--    catch (err) {--}}
        {{--        console.error("Error capturing screen:", err);--}}
        {{--    }--}}
        {{--});--}}

        {{--function sendScreenshotToServer(screenshotData,order_id_for_screenshot) {--}}
        {{--    $.ajax({--}}
        {{--        method: 'post',--}}
        {{--        url: "{{ route('freelancer.order.screenshot.upload') }}",--}}
        {{--        data: {image: screenshotData,order_id_for_screenshot:order_id_for_screenshot},--}}
        {{--        success: function(res) {--}}
        {{--            if (res.status == 'success') {--}}
        {{--                console.log(res.status)--}}
        {{--            }--}}
        {{--        }--}}
        {{--    })--}}
        {{--}--}}
    </script>
@endif

@if(moduleExists('CoinPaymentGateway'))
    <script>
        // $(document).ready(function(){
        //     $(document).on('click', '.playvid-btn', function(){
        //         let vid = $("#back_video")[0];
        //         console.log(vid)
        //         $(this).toggleClass("stop");
        //         $(".playvid-btn i").toggleClass("d-none");
        //         if ($(this).hasClass("stop")) {
        //             vid.play();
        //         } else {
        //             vid.pause();
        //         }
        //     });
        // });
    </script>
@endif

<!--page script-->
@yield('script')
@if(!empty( get_static_option('site_third_party_tracking_code')))
    {!! get_static_option('site_third_party_tracking_code') !!}
@endif
{!! renderBodyEndHooks() !!}
<!-- Load delayed scripts on interaction (Only on Homepage) -->
@if($isHomePage)
<script>
    (function() {
        var lazyJsLoaded = false;
        function loadLazyJs() {
            if (lazyJsLoaded) return;
            lazyJsLoaded = true;
            var scripts = document.querySelectorAll('script[type="text/lazy-js"]');
            var i = 0;
            function loadNext() {
                if (i >= scripts.length) return;
                var oldScript = scripts[i];
                var newScript = document.createElement('script');
                if (oldScript.hasAttribute('data-src')) {
                    newScript.src = oldScript.getAttribute('data-src');
                    newScript.onload = loadNext;
                    newScript.onerror = loadNext;
                } else {
                    newScript.text = oldScript.innerHTML;
                }
                oldScript.parentNode.replaceChild(newScript, oldScript);
                if (!oldScript.hasAttribute('data-src')) {
                    loadNext();
                }
                i++;
            }
            loadNext();
        }
        ['mouseover', 'keydown', 'touchmove', 'touchstart', 'scroll'].forEach(function(e) {
            window.addEventListener(e, loadLazyJs, { once: true, passive: true });
        });
        setTimeout(loadLazyJs, 15000);
    })();
</script>
@endif
</body>

</html>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.display = 'none';
            }
        }, 100);
    });
</script>

<!-- Meta Pixel & GTM (Delayed Execution) -->
<script>
    let thirdPartyScriptsLoaded = false;
    function loadThirdPartyScripts() {
        if (thirdPartyScriptsLoaded) return;
        thirdPartyScriptsLoaded = true;
        
        // Meta Pixel Code
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '134055060620184');
        fbq('track', 'PageView');

        // Google Tag Manager
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NL3SSML5');
    }
    
    window.addEventListener('scroll', loadThirdPartyScripts, {passive: true});
    window.addEventListener('mousemove', loadThirdPartyScripts, {passive: true});
    window.addEventListener('touchstart', loadThirdPartyScripts, {passive: true});
    setTimeout(loadThirdPartyScripts, 10000); // Fallback outside Lighthouse window
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=134055060620184&ev=PageView&noscript=1"
/></noscript>
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NL3SSML5"
    height="0" width="0" style="display:none;visibility:hidden"></iframe>   
</noscript>
<!-- End Meta Pixel & GTM -->
<!-- End Google Tag Manager (noscript) -->