@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($posts as $post)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card">
                @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="Post Image" class="card-img-top img-fluid">
                @endif

                <div class="card-body">
                    <h5 class="card-title">
                        <h1>{{ $post->title }}</h1>
                    </h5>
                    <p class="card-text">
                        @if (strlen($post->content) > 250)
                        {!! substr($post->content, 0, 250) !!}
                        <span id="read-more-{{ $post->id }}" class="read-more-content">...
                            <a href="javascript:void(0);" onclick="expandContent('{{ $post->id }}')">Read more</a>
                        </span>
                        <span id="full-content-{{ $post->id }}" class="full-content" style="display: none;">
                            {!! $post->content !!}
                            <a href="javascript:void(0);" onclick="collapseContent('{{ $post->id }}')">Read less</a>
                        </span>
                        @else
                        {!! $post->content !!}
                        @endif
                    </p>

                    @auth
                    <button class="btn btn-danger" onclick="likePost('{{ $post->id }}')"><i
                            class="far fa-heart"></i></button>
                    @endauth
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">Likes: <span id="like-count-{{ $post->id }}"
                            data-post-id="{{ $post->id }}">{{ $post->likes }}</span></li>
                    <li class="list-group-item">Posted by: {{ $post->author->name }}</li>
                    <li class="list-group-item">Publish: {{ $post->created_at->format('M d, Y') }}</li>
                    <li class="list-group-item">
                        <h5>Comments:</h5>
                        <ul>
                            @foreach ($post->comments as $comment)
                            <li data-comment-id="{{ $comment->id }}">
                                <strong>{{ $comment->author->name }}</strong>: {{ $comment->content }}
                                @auth
                                <button class="btn btn-sm btn-secondary"
                                    onclick="replyToComment({{ $comment->id }})">Reply</button>
                                @endauth

                                @if ($comment->replies->count() > 0)
                                <ul class="list-unstyled">
                                    @foreach ($comment->replies->sortByDesc('created_at') as $reply)
                                    <li>
                                        <strong>{{ $reply->author->name }}</strong>: {{ $reply->content }}
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="list-group-item">@auth
                        <h4>Leave a Comment:</h4>
                        <form action="{{ route('comment.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            <div class="form-group">

                                <textarea name="content" id="content" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success mt-2">Submit</button>
                        </form>
                        @endauth
                    </li>
                </ul>
            </div>
        </div>
        @endforeach

        <div class="d-flex justify-content-center">
            {{ $posts->links('pagination::bootstrap-4') }}
        </div>
    </div>
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

    function expandContent(postId) {
        document.getElementById(`read-more-${postId}`).style.display = 'none';
        document.getElementById(`full-content-${postId}`).style.display = 'inline';
    }

    function collapseContent(postId) {
        document.getElementById(`read-more-${postId}`).style.display = 'inline';
        document.getElementById(`full-content-${postId}`).style.display = 'none';
    }
</script>
@endsection
