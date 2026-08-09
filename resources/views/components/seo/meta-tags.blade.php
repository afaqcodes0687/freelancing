@push('head_scripts')
<meta property="og:title" content="{{ $metaTitle ?? get_static_option('site_title') }}">
<meta property="og:description" content="{{ $metaDescription ?? '' }}">
<meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
<meta property="og:image" content="{{ $ogImage ?? asset('assets/uploads/media-uploader/favicon1731059400.png') }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
@endpush
