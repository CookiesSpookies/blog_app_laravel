<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // get all post
    public function index(){
        return response([
            'posts' => Post::orderBy('created_at','desc')->with('user:id,name,image')->withCount('comments','likes')
            ->with('likes',function($like){
                return $like->where('user_id', auth()->user()->id)
                ->select('id','user_id','post_id')->get();
            })
            ->get()
        ],200);
    }

    //get single post
    public function show($id){
        return response([
            'post'=>Post::where('id',$id)->withCount('comments','likes')->get()
        ]);
    }

    //create a post
    public function store(Request $request){
        //validate fields
        $fields = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image,'posts');

        $post = Post::create([
            'body' => $fields['body'],
            'user_id' =>auth()->user()->id,
            'image' => $image
        ]);

        return response([
            'message' => 'Post created.',
            'post' => $post
        ],200);
    }

    //update a post
    public function update(Request $request, $id){

        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ],403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied.'
            ],403);
        }

        //validate fields
        $fields = $request->validate([
            'body' => 'required|string'
        ]);

        $post->update([
            'body' => $fields['body']
        ]);

        return response([
            'message' => 'Post updated.',
            'post' => $post
        ],200);

    }

    //delete post
    public function destroy($id){

        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ],403);
        }

        if($post->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied.'
            ],403);
        }

        $post->comments()->delete();
        $post->likes()->delete();
        $post->delete();

        return response([
            'message' => 'Post deleted.'
        ],200);

    }
}
