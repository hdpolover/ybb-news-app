<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name'))</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'News and articles')">
    <meta name="keywords" content="@yield('meta_keywords', 'news, articles, blog')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', config('app.name'))">
    <meta property="og:description" content="@yield('og_description', 'News and articles')">
    <meta property="og:image" content="@yield('og_image', asset('img/logo.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Bootstrap Min CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Animate Min CSS -->
    <link rel="stylesheet" href="{{ asset('css/animate.min.css') }}">
    <!-- IcoFont Min CSS -->
    <link rel="stylesheet" href="{{ asset('css/icofont.min.css') }}">
    <!-- MeanMenu CSS -->
    <link rel="stylesheet" href="{{ asset('css/meanmenu.css') }}">
    <!-- Owl Carousel Min CSS -->
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <!-- Magnific Popup Min CSS -->
    <link rel="stylesheet" href="{{ asset('css/magnific-popup.min.css') }}">
    <!-- Style CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Dark CSS -->
    <link rel="stylesheet" href="{{ asset('css/dark.css') }}">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    
    @stack('styles')
</head>
<body>
    
    <!-- Start Header Area -->
    @include('layouts.partials.header')
    <!-- End Header Area -->
    
    <!-- Main Content -->
    @yield('content')
    <!-- End Main Content -->
    
    <!-- Start Footer Area -->
    @include('layouts.partials.footer')
    <!-- End Footer Area -->
    
    <!-- jQuery Min JS -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <!-- Bootstrap Bundle Min JS -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- MeanMenu JS -->
    <script src="{{ asset('js/jquery.meanmenu.js') }}"></script>
    <!-- Owl Carousel Min JS -->
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <!-- Magnific Popup Min JS -->
    <script src="{{ asset('js/jquery.magnific-popup.min.js') }}"></script>
    <!-- WOW Min JS -->
    <script src="{{ asset('js/wow.min.js') }}"></script>
    <!-- Main JS -->
    <script src="{{ asset('js/main.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
