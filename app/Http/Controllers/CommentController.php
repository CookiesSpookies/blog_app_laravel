<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    //get all comments of a post
    public function index($id){
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ],403);
        }

        $comment = $post->comments()->with('user:id,name,image')->get();

        return response([
            'comments' => $comment
        ],200);
    }

    //create a comment
    public function store(Request $request,$id){
        $post = Post::find($id);

        if(!$post){
            return response([
                'message' => 'Post not found'
            ],403);
        }

        //validate fields
        $fields = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $fields['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id,
        ]);

        return response([
            'message' => 'Comment created.'
        ],200);
    }

    //update a comment
    public function update(Request $request, $id){
        $comment = Comment::find($id);

        if(!$comment){
            return response([
                'message' => 'Comment not found'
            ],403);
        }

        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied.'
            ],403);
        }

        //validate fields
        $fields = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $fields['comment']
        ]);

        return response([
            'message' => 'Comment updated.'
        ],200);
    }

    //delete a comment
    public function destroy($id){
        $comment = Comment::find($id);

        if(!$comment){
            return response([
                'message' => 'Comment not found'
            ],403);
        }

        if($comment->user_id != auth()->user()->id){
            return response([
                'message' => 'Permission denied.'
            ],403);
        }

        $comment->delete();

        return response([
            'message' => 'Comment deleted.'
        ],200);
    }
}
