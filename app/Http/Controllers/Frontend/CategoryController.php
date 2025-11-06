<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Term;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Term::where('slug', $slug)
            ->where('type', 'category')
            ->firstOrFail();

        $posts = $category->posts()
            ->where('status', 'published')
            ->where('kind', 'post')
            ->with(['terms', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Term::where('type', 'category')
            ->withCount('posts')
            ->get();

        $popularPosts = Post::where('status', 'published')
            ->where('kind', 'post')
            ->with(['terms', 'author'])
            ->inRandomOrder()
            ->take(5)
            ->get();

        return view('frontend.categories.show', compact(
            'category',
            'posts',
            'categories',
            'popularPosts'
        ));
    }
}
