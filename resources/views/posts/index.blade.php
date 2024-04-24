@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Posts Content</h1>
    <table class="table">
        <a href="{{ route('posts.create') }}" class="btn btn-primary mt-3 mb-3">Create</a>
        <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Author</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td>{{ Str::limit($post->content, 30) }}</td>
                <td>{{ $post->author->name }}</td>
                <td>{{ $post->created_at }}</td>
                <td>{{ $post->updated_at }}</td>
                <td>
                    <a href="{{ route('posts.show', $post) }}" class="btn btn-info">Show</a>
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-warning">Edit</a>

                    <form action="{{ route('posts.destroy', $post) }}" method="post" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $posts->links() }}
    </div>
</div>
@endsection
