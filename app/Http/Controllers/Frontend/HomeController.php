<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured post (most recent published)
        $featuredPost = Post::where('status', 'published')
            ->where('kind', 'post')
            ->with(['terms', 'author'])
            ->orderBy('published_at', 'desc')
            ->first();

        // Get latest posts (excluding featured)
        $latestPosts = Post::where('status', 'published')
            ->where('kind', 'post')
            ->when($featuredPost, fn($q) => $q->where('id', '!=', $featuredPost->id))
            ->with(['terms', 'author'])
            ->orderBy('published_at', 'desc')
            ->take(6)
            ->get();

        // Get popular posts (you can add views count later)
        $popularPosts = Post::where('status', 'published')
            ->where('kind', 'post')
            ->with(['terms', 'author'])
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Get categories with post counts
        $categories = Term::where('type', 'category')
            ->withCount('posts')
            ->take(10)
            ->get();

        return view('frontend.home', compact(
            'featuredPost',
            'latestPosts',
            'popularPosts',
            'categories'
        ));
    }
}
