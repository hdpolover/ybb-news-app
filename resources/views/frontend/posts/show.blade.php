@extends('layouts.frontend')

@section('title', $post->meta_title ?? $post->title)
@section('meta_description', $post->meta_description ?? $post->excerpt)
@section('og_title', $post->og_title ?? $post->title)
@section('og_description', $post->og_description ?? $post->excerpt)
@section('og_image', $post->og_image ?? $post->featured_image)

@section('content')
<!-- Start Page Title Area -->
<div class="page-title-area">
    <div class="container">
        <div class="page-title-content">
            <h2>{{ $post->title }}</h2>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li>Article Details</li>
            </ul>
        </div>
    </div>
</div>
<!-- End Page Title Area -->

<!-- Start Blog Details Area -->
<section class="blog-details-area ptb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="blog-details">
                    <div class="article-image">
                        @if($post->featured_image)
                        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}">
                        @endif
                    </div>
                    
                    <div class="article-content">
                        <ul class="entry-meta">
                            <li>
                                <i class="icofont-user-alt-7"></i>
                                <a href="#">{{ $post->author->name ?? 'Admin' }}</a>
                            </li>
                            <li>
                                <i class="icofont-ui-calendar"></i>
                                {{ $post->published_at->format('F d, Y') }}
                            </li>
                            @if($post->terms->isNotEmpty())
                            <li>
                                <i class="icofont-tag"></i>
                                @foreach($post->terms->where('type', 'category')->take(3) as $category)
                                    <a href="{{ route('categories.show', $category->slug) }}">{{ $category->name }}</a>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </li>
                            @endif
                        </ul>

                        <div class="article-text">
                            {!! $post->content !!}
                        </div>

                        <!-- Tags -->
                        @if($post->terms->where('type', 'tag')->isNotEmpty())
                        <div class="article-tags">
                            <span>Tags:</span>
                            @foreach($post->terms->where('type', 'tag') as $tag)
                                <a href="{{ route('tags.show', $tag->slug) }}">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                        @endif

                        <!-- Share -->
                        <div class="article-share">
                            <div class="row align-items-center">
                                <div class="col-lg-6 col-md-6">
                                    <div class="share-content">
                                        <span>Share:</span>
                                        <ul class="social">
                                            <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank"><i class="icofont-facebook"></i></a></li>
                                            <li><a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $post->title }}" target="_blank"><i class="icofont-twitter"></i></a></li>
                                            <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}" target="_blank"><i class="icofont-linkedin"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Related Posts -->
                @if($relatedPosts->isNotEmpty())
                <div class="related-posts">
                    <h3>Related Articles</h3>
                    <div class="row">
                        @foreach($relatedPosts as $related)
                        <div class="col-lg-6 col-md-6">
                            <div class="single-blog-post">
                                <div class="blog-image">
                                    <a href="{{ route('posts.show', $related->slug) }}">
                                        <img src="{{ $related->featured_image ?? asset('img/blog-home-image/blog-home-1.jpg') }}" alt="{{ $related->title }}">
                                    </a>
                                </div>
                                <div class="blog-post-content">
                                    <h3>
                                        <a href="{{ route('posts.show', $related->slug) }}">
                                            {{ Str::limit($related->title, 60) }}
                                        </a>
                                    </h3>
                                    <span>{{ $related->published_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 col-md-12">
                <aside class="widget-area" id="secondary">
                    <!-- Popular Posts -->
                    <section class="widget widget_sinmun_posts_thumb">
                        <h3 class="widget-title">Popular Posts</h3>
                        @foreach($popularPosts as $popular)
                        <article class="item">
                            <a href="{{ route('posts.show', $popular->slug) }}" class="thumb">
                                <img src="{{ $popular->featured_image ?? asset('img/blog-home-image/blog-home-3.jpg') }}" alt="{{ $popular->title }}">
                            </a>
                            <div class="info">
                                <h4 class="title usmall">
                                    <a href="{{ route('posts.show', $popular->slug) }}">
                                        {{ Str::limit($popular->title, 50) }}
                                    </a>
                                </h4>
                                <time datetime="{{ $popular->published_at }}">
                                    {{ $popular->published_at->format('M d, Y') }}
                                </time>
                            </div>
                        </article>
                        @endforeach
                    </section>

                    <!-- Categories -->
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
                </aside>
            </div>
        </div>
    </div>
</section>
<!-- End Blog Details Area -->
@endsection
