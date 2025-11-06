<footer class="footer-area pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="single-footer-widget">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('img/logo.png') }}" alt="logo" />
                    </a>
                    <p>{{ config('app.name') }} - Your source for the latest news and articles.</p>
                    <ul class="social">
                        <li><a href="#" target="_blank"><i class="icofont-facebook"></i></a></li>
                        <li><a href="#" target="_blank"><i class="icofont-twitter"></i></a></li>
                        <li><a href="#" target="_blank"><i class="icofont-instagram"></i></a></li>
                        <li><a href="#" target="_blank"><i class="icofont-linkedin"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="single-footer-widget">
                    <h3>Quick Links</h3>
                    <ul class="quick-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('posts.index') }}">Articles</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="single-footer-widget">
                    <h3>Categories</h3>
                    <ul class="quick-links">
                        @php
                            $footerCategories = \App\Models\Term::where('type', 'category')
                                ->take(5)
                                ->get();
                        @endphp
                        @foreach($footerCategories as $category)
                            <li>
                                <a href="{{ route('categories.show', $category->slug) }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="single-footer-widget">
                    <h3>Newsletter</h3>
                    <p>Subscribe to get the latest news and updates.</p>
                    <form class="newsletter-form">
                        @csrf
                        <input type="email" class="form-control" placeholder="Your Email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="copyright-area">
    <div class="container">
        <div class="copyright-area-content">
            <p>
                &copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.
            </p>
        </div>
    </div>
</div>
