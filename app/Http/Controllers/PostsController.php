<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Post;
use App\Like;

class PostsController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.show', compact('post'));
    }


    public function create()
    {
        return view('posts.create');
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'title'     => 'required|max:255',
            'description'  => 'required|max:255'
        ]);

        Post::create([
            'title'     => $request->input('title'),
            'description' => $request->input('description'),
            'user_id'   => Auth::user()->id,
        ]);

        return redirect('/posts')->withSuccess('post was created successfully');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }


    public function update(Request $request, $post_id = null)
    {
        $this->validate($request, [
            'title'       => 'max:255',
            'description' => 'required|max:255',
        ]);

        if($post_id == null)
        $post = Post::find($request['postId']);
        else 
        $post = Post::find($post_id);
        $post->update($request->all());
        return redirect('/posts')->withSuccess('Post info updated successfully.');
    }

    public function destroy(Post $post)
    {
        if(Auth::user() == $post->user)
        {
            $post->delete();
            return redirect('/posts')->withSuccess('success', 'Post was deleted');
        } else {
            return back()->with('error', 'You don\'t have the permission for this post' );
        }
    }

    //previousElementSibling == null => like
    //previousElementSibling != null => dislike
    public function postLikePost(Request $request)
    {
        $post_id = $request['postId'];
        $is_like = $request['isLike'] === 'true';//we have got it as string
        //dd($post_id,$is_like);
        $update = false;
        $post = Post::find($post_id);
        if (!$post) {
            return null;
        }
        $user = Auth::user();
        $like_from_user = $user->likes()->where('post_id', $post_id)->first();
        //(1)- already interacted
        if ($like_from_user) {
            $already_like = $like_from_user->like;
            $update = true;
            //already like
            if ($already_like == $is_like) {
                $like_from_user->delete();
                return null;
            }
        } 
        //(2)- not interacted
        else {
            $like_from_user = new Like();
        }
        
        $like_from_user->like = $is_like;
        $like_from_user->user_id = $user->id;
        $like_from_user->post_id = $post->id;
        //(1)  dislike update  -> like , vice versa
        if ($update) {
            $like_from_user->update();
        } else {
            //(2)
            $like_from_user->save();
        }
        return null;
    }

}
