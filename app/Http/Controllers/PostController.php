<?php

namespace App\Http\Controllers;

use App\Models\Post;
// use App\Mail\NewPostEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNewPostEmail;
use Illuminate\Support\Facades\Mail;

class PostController extends Controller
{

    // GET Search posts
    public function search($term) {
        $posts = Post::search($term)->get();
        $posts->load('user:id,username,avatar');
        return $posts;
    }

    // GET Edit post form
    public function showEditForm(Post $post) {
        return view('edit-post', [
            'post' => $post
        ]);
    }

    // PUT/UPDATE post
    public function actuallyUpdate(Post $post, Request $request) {
        $incomingFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['body'] = strip_tags($incomingFields['body']);
        $post->update($incomingFields);
        return back()->with('successMessage', 'Post successfully updated');
    }


    // DELETE Post
    public function delete(Post $post) {
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

        // send JOB
        dispatch(new SendNewPostEmail([
            'sendTo' => auth()->user()->email,
            'name' => auth()->user()->username,
            'title' => $newPost->title,
        ]));

        // send EMAIL
        // Mail::to(auth()->user()->email)->send(new NewPostEmail([
        //     'name' => auth()->user()->username,
        //     'title' => $newPost->title,
        // ]));

        return redirect("/post/{$newPost->id}")->with('successMessage', 'New post successfully created.');
    }

    // GET Post 2
    public function viewSinglePost(Post $post) {
        
        $ourHTML = Str::markdown($post->body);
        $post['body'] = strip_tags( $ourHTML, '<p><ul><li><ol><strong><em><h3><br><h2>>'); // tags that are allowed
        return view('single-post', ['post' => $post]);
    }

}
