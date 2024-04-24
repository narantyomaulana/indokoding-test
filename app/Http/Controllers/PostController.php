<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);

        return view('posts.index', compact('posts'));
    }


    public function create()
    {
        return view('posts.create');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $post = new Post();
        $post->title = $request->input('title');
        $post->content = $request->input('content');
        $post->author_id = $request->user()->id;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            $post->image = $imagePath;
        }

        $post->save();

        return redirect()->route('content.index')->with('success', 'Post created successfully.');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $users = User::all();

        return view('posts.edit', compact('post', 'users'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $post->title = $request->input('title');
        $post->content = $request->input('content');

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('uploads', 'public');
            $post->image = $newImagePath;
        }

        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return redirect()->route('posts.index')->with('error', 'Pos tidak ditemukan.');
        }

        // Ambil semua komentar yang terkait dengan post
        $comments = $post->comments;

        // Iterasi dan hapus semua balasan yang terkait dengan setiap komentar
        foreach ($comments as $comment) {
            $comment->replies()->delete();
        }

        // Hapus semua komentar yang terkait dengan pos
        $post->comments()->delete();

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Pos dan komentar terkait berhasil dihapus.');
    }



    public function likePost(Request $request)
    {
        $postId = $request->input('post_id');
        $post = Post::find($postId);

        if (!$post) {
            return response()->json(['error' => 'Post not found'], 404);
        }

        $post->likes += 1;
        $post->save();

        return response()->json(['likes' => $post->likes]);
    }



}
