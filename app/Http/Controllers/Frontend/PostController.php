<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->where('kind', 'post')
            ->with(['terms', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Term::where('type', 'category')
            ->withCount('posts')
            ->get();

        return view('frontend.posts.index', compact('posts', 'categories'));
    }

    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->with(['terms', 'author'])
            ->firstOrFail();

        // Get related posts (same categories)
        $categoryIds = $post->terms()->where('type', 'category')->pluck('terms.id');
        
        $relatedPosts = Post::where('status', 'published')
            ->where('kind', 'post')
            ->where('id', '!=', $post->id)
            ->whereHas('terms', fn($q) => $q->whereIn('terms.id', $categoryIds))
            ->with(['terms', 'author'])
            ->take(4)
            ->get();

        // Get popular posts
        $popularPosts = Post::where('status', 'published')
            ->where('kind', 'post')
            ->with(['terms', 'author'])
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Get categories
        $categories = Term::where('type', 'category')
            ->withCount('posts')
            ->take(10)
            ->get();

        return view('frontend.posts.show', compact(
            'post',
            'relatedPosts',
            'popularPosts',
            'categories'
        ));
    }
}
