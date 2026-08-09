<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Module PluginManage</title>

       {{-- Laravel Mix - CSS File --}}
       {{-- <link rel="stylesheet" href="{{ mix('css/pluginmanage.css') }}"> --}}

    </head>
    <body>
        @yield('content')

        {{-- Laravel Mix - JS File --}}
        {{-- <script src="{{ mix('js/pluginmanage.js') }}"></script> --}}

        {{-- Google Analytics GT4 --}}
        @if(get_static_option('google_analytics_gt4_status') == 'on' && get_static_option('google_analytics_gt4_ID'))
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ get_static_option('google_analytics_gt4_ID') }}"></script>
            <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '{{ get_static_option('google_analytics_gt4_ID') }}');
            </script>
        @endif
    </body>
</html>
