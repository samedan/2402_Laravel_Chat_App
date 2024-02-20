<x-layout :doctitle="$post->title">

    <div class="container py-md-5 container--narrow">
      <div class="d-flex justify-content-between">
        <h2>{{$post->title}}</h2>

        {{-- Check if user can update a post in PostPolicy->AuthServiceProvider --}}
        @can('update', $post)
        <span class="pt-2">
          <a href="/post/{{$post->id}}/edit" class="text-primary mr-2" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>
          <form class="delete-post-form d-inline" action="/post/{{$post->id}}" method="POST">
            @csrf
            @method('DELETE')
            <button class="delete-post-button text-danger" data-toggle="tooltip" data-placement="top" title="Delete Post"><i class="fas fa-trash"></i></button>
          </form>
        </span>
        @endcan
        {{-- END Check if user can update a post in PostPolicy --}}

      </div>

      <p class="text-muted small mb-4">
        <a href="/profile/{{$post->userThatWroteThePost->username}}"><img class="avatar-tiny" src="{{$post->userThatWroteThePost->avatar}}" /></a>
        Posted by <a href="/profile/{{$post->userThatWroteThePost->username}}">{{$post->userThatWroteThePost->username}}</a> on {{$post->created_at->format('n/j/Y')}}
      </p>

      <div class="body-content">
        {!! $post->body !!}
      </div>
    </div>

   </x-layout>
