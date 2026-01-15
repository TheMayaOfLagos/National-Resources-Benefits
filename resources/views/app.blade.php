<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Dynamic SEO Meta Tags --}}
    @php
        $siteName = \App\Models\Setting::get('site_name', 'National Resource Benefits');
        $metaTitle = \App\Models\Setting::get('seo_meta_title', $siteName);
        $metaDescription = \App\Models\Setting::get('seo_meta_description', 'A secure gateway ensuring federal funds reach the intended recipients.');
        $metaKeywords = \App\Models\Setting::get('seo_meta_keywords');
        $ogImage = \App\Models\Setting::get('seo_og_image');
        $favicon = \App\Models\Setting::get('site_favicon');
        $googleAnalytics = \App\Models\Setting::get('seo_google_analytics');
        $googleTagManager = \App\Models\Setting::get('seo_google_tag_manager');
        $facebookPixel = \App\Models\Setting::get('seo_facebook_pixel');
    @endphp

    <title inertia>{{ $siteName }}</title>

    {{-- Default Meta Description --}}
    <meta name="description" content="{{ $metaDescription }}">

    {{-- Meta Keywords (if set) --}}
    @if($metaKeywords)
        @if(is_array($metaKeywords))
            <meta name="keywords" content="{{ implode(', ', $metaKeywords) }}">
        @else
            <meta name="keywords" content="{{ $metaKeywords }}">
        @endif
    @endif

    {{-- Open Graph / Social Meta Tags --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    @if($ogImage)
        <meta property="og:image" content="{{ asset('storage/' . $ogImage) }}">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    @if($ogImage)
        <meta name="twitter:image" content="{{ asset('storage/' . $ogImage) }}">
    @endif

    {{-- Favicon --}}
    @if($favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Google Tag Manager (Head) --}}
    @if($googleTagManager)
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || []; w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                }); var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ $googleTagManager }}');</script>
    @endif

    {{-- Google Analytics --}}
    @if($googleAnalytics)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleAnalytics }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '{{ $googleAnalytics }}');
        </script>
    @endif

    {{-- Facebook Pixel --}}
    @if($facebookPixel)
        <script>
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return; n = f.fbq = function () {
                    n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
                n.queue = []; t = b.createElement(e); t.async = !0;
                t.src = v; s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $facebookPixel }}');
            fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id={{ $facebookPixel }}&ev=PageView&noscript=1" /></noscript>
    @endif

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    {{-- Google Tag Manager (noscript - Body) --}}
    @if($googleTagManager)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $googleTagManager }}" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
    @endif

    @inertia
</body>

</html>