@extends('layouts.app')
@section('content')
<h1>All Posts</h1>

@foreach ($posts as $post)    
<article class="post" data-postid="{{ $post->id }}">       
<div class="col-lg-6 col-md-12">
<div class="card">
  <div class="card-header card-header-warning">
    <h2 class="card-title"><a href="{{ url('/posts', $post->id) }}"><h2>{{ $post->title }}</h2></a></h2>
  </div>
  <div class="card-body table-responsive">
    <table class="table table-hover">
      <tbody>
      <p><h5>{{ $post->description }}</h5></p>
      <div class="info">
       <h8>Posted by {{ $post->user->first_name }} on {{ $post->created_at }}</h8>
       </div>
       <div class="interaction">
            <a href="#" class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 1 ? 'You like this post' : 'Like' : 'Like'  }}</a> |
            <a href="#" class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 0 ? 'You don\'t like this post' : 'Dislike' : 'Dislike'  }}</a>
       </div>
       @if(Auth::user() == $post->user)
              |
              <a href="{{route('post.edit', $post)}}" class="edit">Edit</a> |
              <a href="{{ route('post.delete', ['post_id' => $post->id]) }}">Delete</a>
       @endif              
      </tbody>
    </table>
  </div>
</div>
</div>
</article>
@endforeach
{{ $posts->links() }}

<script>
        var token = '{{ Session::token() }}';
        var urlLike = '{{ route('like') }}';
</script>
@endsection