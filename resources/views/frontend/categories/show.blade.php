@extends('layouts.frontend')

@section('title', $category->name . ' - ' . config('app.name'))
@section('meta_description', $category->description ?? 'Browse articles in ' . $category->name)

@section('content')
<!-- Start Page Title Area -->
<div class="page-title-area">
    <div class="container">
        <div class="page-title-content">
            <h2>{{ $category->name }}</h2>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>{{ $category->name }}</li>
            </ul>
        </div>
    </div>
</div>
<!-- End Page Title Area -->

<!-- Start Category Area -->
<section class="category-area ptb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                @if($category->description)
                <div class="category-description mb-30">
                    <p>{{ $category->description }}</p>
                </div>
                @endif

                @if($posts->count() > 0)
                <div class="row">
                    @foreach($posts as $post)
                    <div class="col-lg-6 col-md-6">
                        <div class="single-blog-post mb-30">
                            <div class="blog-image">
                                <a href="{{ route('posts.show', $post->slug) }}">
                                    <img src="{{ $post->featured_image ?? asset('img/blog-home-image/blog-home-1.jpg') }}" alt="{{ $post->title }}">
                                </a>
                                <div class="date">
                                    <i class="icofont-ui-calendar"></i> {{ $post->published_at->format('M d, Y') }}
                                </div>
                            </div>
                            <div class="blog-post-content">
                                <h3>
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <span>By <a href="#">{{ $post->author->name ?? 'Admin' }}</a></span>
                                <p>{{ Str::limit($post->excerpt, 120) }}</p>
                                <a href="{{ route('posts.show', $post->slug) }}" class="read-more-btn">
                                    Read More <i class="icofont-double-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pagination-area">
                    {{ $posts->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    No articles found in this category yet.
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-md-12">
                <aside class="widget-area" id="secondary">
                    <!-- Search Widget -->
                    <section class="widget widget_search">
                        <form class="search-form">
                            <input type="search" class="search-field" placeholder="Search...">
                            <button type="submit"><i class="icofont-search-1"></i></button>
                        </form>
                    </section>

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
                            @foreach($categories as $cat)
                            <li class="{{ $cat->id === $category->id ? 'active' : '' }}">
                                <a href="{{ route('categories.show', $cat->slug) }}">
                                    {{ $cat->name }} 
                                    <span class="post-count">({{ $cat->posts_count }})</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </section>

                    <!-- Tags Widget -->
                    @php
                        $tags = \App\Models\Term::where('type', 'tag')->take(15)->get();
                    @endphp
                    @if($tags->count() > 0)
                    <section class="widget widget_tag_cloud">
                        <h3 class="widget-title">Tags</h3>
                        <div class="tagcloud">
                            @foreach($tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    </section>
                    @endif

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
<!-- End Category Area -->
@endsection
