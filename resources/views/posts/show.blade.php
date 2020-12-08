@extends('layouts.app')


@section('content')

    <h2>{{ $post->title }}</h2>
    <p>{{ $post->description }}</p>

    @if(Auth::user() == $post->user)
    <div class="btn-group">
    <form action="/posts/{{ $post->id }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <button type="submit" class="btn btn-flat btn-danger">Delete Post</button>
    </form> 
    </div>
    
    <div class="btn-group">
    <form action="/posts/{{ $post->id }}/edit" method="GET">
        <button type="submit" class="btn btn-primary">Edit Post</button>
    </form>
    </div>
    @endif

@endsection

   