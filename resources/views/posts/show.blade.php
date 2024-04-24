@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $post->title }}</h1>

    @if ($post->image)
    <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" class="img-fluid">
    @endif

    <p>{!! $post->content !!}</p>

    <h4>Posted by: {{ $post->author->name }} on {{ $post->created_at->format('M d, Y') }}</h4>

    <h5>Likes: <span id="like-count-{{ $post->id }}" data-post-id="{{ $post->id }}">{{ $post->likes }}</span></h5>
    @auth
    <button class="btn btn-primary mb-2" onclick="likePost('{{ $post->id }}')">Like</button>
    @endauth
    <h5>Comments:</h5>
    <ul>
        @foreach ($post->comments as $comment)
        <li data-comment-id="{{ $comment->id }}">
            <strong>{{ $comment->author->name }}</strong>: {{ $comment->content }}
            @auth
            <button class="btn btn-sm btn-secondary mt-2" onclick="replyToComment({{ $comment->id }})">Reply</button>
            @endauth

            <ul class="list-unstyled">
                @foreach ($comment->replies->sortByDesc('created_at') as $reply)
                <li>
                    <strong>{{ $reply->author->name }}</strong>: {{ $reply->content }}
                </li>
                @endforeach
            </ul>
        </li>
        @endforeach
    </ul>

    @auth
    <hr>
    <h4>Leave a Comment:</h4>
    <form action="{{ route('comment.store') }}" method="post">
        @csrf
        <input type="hidden" name="post_id" value="{{ $post->id }}">
        <div class="form-group">
            <label for="content">Comment</label>
            <textarea name="content" id="content" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
        <a href="{{ route('comment.index') }}" class="btn btn-danger mt-3">Back</a>
    </form>
    @endauth
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function likePost(postId) {
        $.ajax({
            type: 'POST',
            url: '{{ route('like.post') }}',
            data: {
            _token: '{{ csrf_token() }}',
            post_id: postId
        },
        success: function (response) {
            const likeCountElement = document.getElementById(`like-count-${postId}`);
            likeCountElement.textContent = response.likes;
        },
        error: function (error) {
            console.error(error);
        }
        });
    }

    function replyToComment(commentId) {
        const commentElement = document.querySelector(`li[data-comment-id="${commentId}"]`);
        const existingForm = commentElement.querySelector('.reply-form');


        if (!existingForm) {
            const replyForm = document.createElement('form');
            replyForm.className = 'reply-form';
            replyForm.action = `/comments/${commentId}/replies`;
            replyForm.method = 'POST';
            replyForm.innerHTML = `
            @csrf
            <div class="form-group">
                <label for="reply-content">Your reply:</label>
                <textarea name="content" id="reply-content" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Reply</button>
            `;

            commentElement.appendChild(replyForm);
        }
    }
</script>
@endsection
