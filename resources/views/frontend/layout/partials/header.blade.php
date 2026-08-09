
<!DOCTYPE html>
<html lang="{{get_user_lang()}}" dir="{{get_user_lang_direction()}}">

<head>
    <script type="text/javascript">
        // Delayed execution for third-party scripts to avoid blocking main thread
        let clarityLoaded = false;
        function loadClarity() {
            if (clarityLoaded) return;
            clarityLoaded = true;
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "xj7yi1z460");
        }
        window.addEventListener('scroll', loadClarity, {passive: true});
        window.addEventListener('mousemove', loadClarity, {passive: true});
        window.addEventListener('touchstart', loadClarity, {passive: true});
        setTimeout(loadClarity, 15000); // Fallback outside Lighthouse window
    </script>
    {!! renderHeadStartHooks() !!}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="facebook-domain-verification" content="lxm2inpags9h7ldznbozuq8v6y2lzj" />
    
    <!-- Preconnect for external resources -->
    <link rel="preconnect" href="https://www.clarity.ms" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php
        $canonical = request()->url();
        if (request()->has('page') && request()->page > 1) {
            $canonical .= '?page=' . request()->page;
        }
    @endphp
    <link rel="canonical" href="{{$canonical}}" />
@yield('meta')
    <!-- favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/uploads/media-uploader/favicon1731059400.png') }}" sizes="32x32">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/uploads/media-uploader/favicon1731059400.png') }}">
{{--    @php--}}
{{--        $site_favicon = get_attachment_image_by_id(get_static_option('site_favicon'),'full',false);--}}
{{--    @endphp--}}
{{--    <!-- !empty($site_favicon) -->--}}
{{--    @if(!empty($site_favicon))--}}
{{--        <link rel="icon" href="{{$site_favicon['img_url'] ?? ''}}" sizes="40x40" type="icon/png">--}}
{{--    @endif--}}          

{{--    {!! load_google_fonts() !!}--}}
    <link rel="stylesheet" href="{{asset('assets/common/css/font.css')}}" media="print" onload="this.media='all'">

    @if(request()->routeIs('homepage'))
    <link rel="preload" as="image" href="/assets/static/img/banner/rating-star.webp" type="image/webp" fetchpriority="high">
    @endif

    <!-- preload critical css -->
    <link rel="preload" href="{{ asset('assets/common/css/bootstrap.min.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/frontend/css/style.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/frontend/css/helpers.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/rf/css/main.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/rf/css/dbresponsive.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/rf/css/custom.css') }}" as="style">
    
    <!-- bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/common/css/bootstrap.min.css') }}">
    <!-- Animate Css (Deferred) -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/animate.css') }}" media="print" onload="this.media='all'">
    <!-- Slick -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/slick.css') }}" media="print" onload="this.media='all'">
    <!-- All Plugin Css -->
    <link rel="stylesheet" href="{{ asset('assets/common/css/all_plugin.css') }}" media="print" onload="this.media='all'">
    <!-- Toastr Css (Deferred) -->
    <link rel="stylesheet" href="{{ asset('assets/common/css/toastr.min.css') }}" media="print" onload="this.media='all'">
    <!-- Helper Css -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/helpers.css')}}">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/magnific-popup.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('assets/frontend/css/style.css') }}">

    <!-- old home page css --->
    <link rel="stylesheet" href="{{ asset('assets/rf/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/rf/css/dbresponsive.css') }}">    
    <!-- old home page css end --->
    
    <link rel="stylesheet" href="{{ asset('assets/rf/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/rf/css/icons.css') }}">
      



    @if(get_user_lang_direction() == 'rtl')
        <link rel="stylesheet" href="{{ asset('assets/frontend/css/right-rtl.css') }}">
    @endif
    @include('frontend.layout.partials.root-style')
    <!-- page css -->
    @yield('style')

    @if(isset($customSeoSetting) && $customSeoSetting)
        <title>{{ $customSeoSetting->meta_title ?? get_static_option('site_title') }}</title>
        @if($customSeoSetting->meta_description)
            <meta name="description" content="{{ $customSeoSetting->meta_description }}">
        @endif
        @if($customSeoSetting->meta_keywords)
            <meta name="keywords" content="{{ $customSeoSetting->meta_keywords }}">
        @endif
    @elseif(request()->routeIs('homepage'))
        <title>{{get_static_option('site_title')}} - {{get_static_option('site_tag_line')}}</title>

        {!! render_site_meta() !!}

    @elseif( request()->routeIs('frontend.dynamic.page') && $page_type === 'page' )

        {!! render_site_title(optional($page_post)->title ) !!}
        {!! render_site_meta() !!}

    @else
        <title>@yield('site_title')</title>
        @if(View::hasSection('meta_title'))
            <meta name="title" content="@yield('meta_title')">
        @endif
        @if(View::hasSection('meta_description'))
            <meta name="description" content="@yield('meta_description')">
        @endif
        @if(View::hasSection('meta_keywords'))
            <meta name="keywords" content="@yield('meta_keywords')">
        @endif
    @endif
@php

    $custom_css = '';
    if (file_exists('assets/frontend/css/dynamic-style.css')) {
        $custom_css = file_get_contents('assets/frontend/css/dynamic-style.css');
    }
    @endphp
    @if(!empty($custom_css))
        <link rel="stylesheet" href="{{asset('assets/frontend/css/dynamic-style.css')}}">
    @endif
    {!! renderHeadEndHooks() !!}
    @stack('head_scripts')
</head>

<body>
{!! renderBodyStartHooks() !!}