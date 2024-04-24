<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);

        return view('comments.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content' => 'required',
            'post_id' => 'required|exists:posts,id',
        ]);

        $comment = new Comment([
            'content' => $request->content,
            'post_id' => $request->post_id,
            'author_id' => Auth::user()->id,
        ]);
        $comment->save();

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    public function edit(Comment $comment)
    {
        // Authorization check (optional)
        $this->authorize('update', $comment);

        return view('comments.edit', compact('comment'));
    }


    public function destroy(Comment $comment)
    {
        // Authorization check (optional)
        $this->authorize('delete', $comment);

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }

}
