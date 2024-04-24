<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    public function store(Request $request, $commentId)
    {
        $this->validate($request, [
            'content' => 'required',
        ]);

        $reply = new Reply([
            'content' => $request->content,
            'comment_id' => $commentId,
            'author_id' => Auth::user()->id,
        ]);
        $reply->save();

        return redirect()->back()->with('success', 'Reply added successfully!');
    }
}
