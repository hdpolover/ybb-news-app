<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class APIPostController extends Controller
{
    public function list($tenant_id)
    {
        $posts = Post::with('author')
            ->where('tenant_id', $tenant_id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($posts);
    }

    public function read($tenant_id, $slug)
    {
        $post = Post::with('author')
            ->where('tenant_id', $tenant_id)
            ->where('slug', $slug)
            ->first();

        return response()->json($post);
    }
}
