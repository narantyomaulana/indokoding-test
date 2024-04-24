<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(3);
        return view('contentArtikel.index', compact('posts'));
    }

}
