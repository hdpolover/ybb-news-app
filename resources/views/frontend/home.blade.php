@extends('layouts.frontend')

@section('title', 'Home - ' . config('app.name'))

@section('content')
<!-- Start Main News Area -->
<section class="main-news-area ptb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <!-- Featured Post -->
                @if($featuredPost)
                <div class="main-news-item mb-30">
                    <a href="{{ route('posts.show', $featuredPost->slug) }}">
                        <img src="{{ $featuredPost->featured_image ?? asset('img/blog-home-image/blog-home-1.jpg') }}" alt="{{ $featuredPost->title }}">
                    </a>
                    <div class="main-news-content">
                        <a href="{{ route('categories.show', $featuredPost->terms->first()->slug ?? '#') }}" class="news-tag">
                            {{ $featuredPost->terms->first()->name ?? 'News' }}
                        </a>
                        <h3>
                            <a href="{{ route('posts.show', $featuredPost->slug) }}">
                                {{ $featuredPost->title }}
                            </a>
                        </h3>
                        <ul class="meta-list">
                            <li><i class="icofont-user-alt-7"></i> {{ $featuredPost->author->name ?? 'Admin' }}</li>
                            <li><i class="icofont-ui-calendar"></i> {{ $featuredPost->published_at->format('M d, Y') }}</li>
                        </ul>
                        <p>{{ $featuredPost->excerpt }}</p>
                    </div>
                </div>
                @endif

                <!-- Latest Posts Grid -->
                <div class="row">
                    @foreach($latestPosts as $post)
                    <div class="col-lg-6 col-md-6">
                        <div class="single-main-news-item mb-30">
                            <a href="{{ route('posts.show', $post->slug) }}">
                                <img src="{{ $post->featured_image ?? asset('img/blog-home-image/blog-home-2.jpg') }}" alt="{{ $post->title }}">
                            </a>
                            <div class="main-news-content">
                                <a href="{{ route('categories.show', $post->terms->first()->slug ?? '#') }}" class="news-tag">
                                    {{ $post->terms->first()->name ?? 'News' }}
                                </a>
                                <h3>
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        {{ Str::limit($post->title, 60) }}
                                    </a>
                                </h3>
                                <ul class="meta-list">
                                    <li><i class="icofont-user-alt-7"></i> {{ $post->author->name ?? 'Admin' }}</li>
                                    <li><i class="icofont-ui-calendar"></i> {{ $post->published_at->format('M d, Y') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-md-12">
                <aside class="widget-area" id="secondary">
                    <!-- Popular Posts Widget -->
                    <section class="widget widget_sinmun_posts_thumb">
                        <h3 class="widget-title">Popular Posts</h3>
                        @foreach($popularPosts as $post)
                        <article class="item">
                            <a href="{{ route('posts.show', $post->slug) }}" class="thumb">
                                <img src="{{ $post->featured_image ?? asset('img/blog-home-image/blog-home-3.jpg') }}" alt="{{ $post->title }}">
                            </a>
                            <div class="info">
                                <h4 class="title usmall">
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        {{ Str::limit($post->title, 50) }}
                                    </a>
                                </h4>
                                <time datetime="{{ $post->published_at }}">
                                    {{ $post->published_at->format('M d, Y') }}
                                </time>
                            </div>
                        </article>
                        @endforeach
                    </section>

                    <!-- Categories Widget -->
                    <section class="widget widget_categories">
                        <h3 class="widget-title">Categories</h3>
                        <ul>
                            @foreach($categories as $category)
                            <li>
                                <a href="{{ route('categories.show', $category->slug) }}">
                                    {{ $category->name }} 
                                    <span class="post-count">({{ $category->posts_count }})</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </section>

                    <!-- Newsletter Widget -->
                    <section class="widget widget_newsletter">
                        <h3 class="widget-title">Newsletter</h3>
                        <p>Subscribe to get the latest news</p>
                        <form class="newsletter-form">
                            @csrf
                            <input type="email" class="form-control" placeholder="Your Email" required>
                            <button type="submit" class="btn btn-primary">Subscribe</button>
                        </form>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</section>
<!-- End Main News Area -->
@endsection
