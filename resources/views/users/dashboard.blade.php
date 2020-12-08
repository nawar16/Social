@extends('layouts.app')

@section('content')
    @include('includes.message-block')
    <section class="row new-post">
        <div class="col-md-6 col-md-offset-3">
            <header><h3>What do you have to say?</h3></header>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/posts') }}">
            {!! csrf_field() !!}
            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                <label for="title" class="col-md-8">Title</label>

                <div class="col-md-10">
                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}">

                    @if ($errors->has('title'))
                        <span class="help-block">
                            <strong>{{ $errors->first('title') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                <label for="description" class="col-md-8">Description</label>

                <div class="col-md-10">
                    <textarea type="text" class="form-control" name="description" value="{{ old('description') }}">
                    </textarea>

                    @if ($errors->has('description'))
                        <span class="help-block">
                            <strong>{{ $errors->first('description') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-6 col-md-offset-4" style="text-align: right">
                    <button type="submit" class="btn btn-primary">
                        Create Post
                    </button>
                </div>
            </div>
        </form>
        </div>
    </section>
    <section class="row posts">
        <div class="col-md-6 col-md-offset-3">
            <header><h3>What other people say...</h3></header>
            @foreach ($posts as $post)   
            <div class="col-lg-6 col-md-12">
            <div class="card">
              <div class="card-header card-header-warning">
                   <a href="{{ url('/posts', $post->id) }}">
                     <h2>{{ $post->title }}</h2>
                   </a>
              </div>
            <article class="post"  data-postid="{{ $post->id }}">
                    <h4>{{ $post->description }}</h4>
                    <div class="info">
                        Posted by {{ $post->user->name }} on {{ $post->created_at }}
                    </div>
                    <div class="interaction">
                        <a href="#" class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 1 ? 'You like this post' : 'Like' : 'Like'  }}</a> |
                        <a href="#" class="like">{{ Auth::user()->likes()->where('post_id', $post->id)->first() ? Auth::user()->likes()->where('post_id', $post->id)->first()->like == 0 ? 'You don\'t like this post' : 'Dislike' : 'Dislike'  }}</a>
                        @if(Auth::user() == $post->user)
                        |
                        <a href="#" class="edit">Edit</a> |
                        <a href="{{ route('post.delete', ['post' => $post]) }}">Delete</a>
                        @endif
                    </div>
            </article>
            </div>
            </div>
            @endforeach
        </div>
    </section>

    <div class="modal fade" tabindex="-1" role="dialog" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Post</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="post-title">Edit the title</label>
                            <textarea class="form-control" name="post-title" id="post-title" rows="5"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="post-body">Edit the descreption</label>
                            <textarea class="form-control" name="post-body" id="post-body" rows="5"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modal-save">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        var token = '{{ Session::token() }}';
        var urlEdit = '{{ route('update') }}';
        var urlLike = '{{ route('like') }}';
    </script>
@endsection