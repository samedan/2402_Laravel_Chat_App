<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{

    // DELETE Post
    public function delete(Post $post) {
        if( auth()->user()->cannot('delete', $post)) {
            return 'You cannot delete the post';
        }
        $post->delete();
        return redirect('/profile/'.auth()->user()->username)->with('successMessage', 'Post succesfully deleted');
        
    }

    // GET Create Post Form
    public function showCreateForm() {
        return view('create-post');
    }

    // POST Post
    public function storeNewPost(Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $incomingFields['user_id'] = auth()->id();
        
        $newPost = Post::create($incomingFields);
        return redirect("/post/{$newPost->id}")->with('successMessage', 'New post successfully created.');
    }

    // GET Post 2
    public function viewSinglePost(Post $post) {
        
        $ourHTML = Str::markdown($post->body);
        $post['body'] = strip_tags( $ourHTML, '<p><ul><li><ol><strong><em><h3><br><h2>>'); // tags that are allowed
        return view('single-post', ['post' => $post]);
    }

}
