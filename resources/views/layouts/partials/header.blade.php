<header class="header-area">
    <div class="top-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-8">
                    <ul class="top-nav">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('posts.index') }}">Articles</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-6 col-md-4 text-end">
                    <ul class="top-social">
                        <li><a href="#" target="_blank"><i class="icofont-facebook"></i></a></li>
                        <li><a href="#" target="_blank"><i class="icofont-twitter"></i></a></li>
                        <li><a href="#" target="_blank"><i class="icofont-instagram"></i></a></li>
                    </ul>
                    <div class="header-date"><i class="icofont-clock-time"></i> {{ now()->format('l, F d, Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="navbar-area">
        <div class="sinmun-mobile-nav">
            <div class="logo">
                <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt="logo" /></a>
            </div>
        </div>
        <div class="sinmun-nav">
            <div class="container">
                <nav class="navbar navbar-expand-md navbar-light">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        <img src="{{ asset('img/logo.png') }}" alt="logo" />
                    </a>
                    <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('posts.index') }}" class="nav-link {{ request()->routeIs('posts.*') ? 'active' : '' }}">News</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">Categories</a>
                                <ul class="dropdown-menu">
                                    @php
                                        $categories = \App\Models\Term::where('type', 'category')
                                            ->take(10)
                                            ->get();
                                    @endphp
                                    @foreach($categories as $category)
                                        <li class="nav-item">
                                            <a href="{{ route('categories.show', $category->slug) }}" class="nav-link">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">About</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">Contact</a>
                            </li>
                        </ul>
                        <div class="others-option">
                            <div class="search-box"><i class="icofont-search-1"></i></div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
