<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // GET Form
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
        Post::create($incomingFields);
        return 'post saved';
    }

    // GET Post 2
    public function viewSinglePost(Post $post) {
        return view('single-post', ['post' => $post]);
    }

}
