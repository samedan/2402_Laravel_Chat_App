<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    //POST Image Avatar
    public function storeAvatar(Request $request) {
        $request->validate([
            'avatar' => 'required|image|max:3000'
        ]);

        $user = auth()->user();
        $filename = $user->id . '-' . uniqid() . '.jpg';
        // raw data 120x120 pixels
        $imgData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');
        Storage::put('public/avatars/'.$filename, $imgData);

        // delete the old avatar file
        $oldAvatar = $user->avatar;

        $user->avatar = $filename;
        $user->save();

        // really delete the old avatar file
        if($oldAvatar != '/fallback-avatar.jpg') {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }
        return back()->with('successMessage', 'Congrats on the new avatar');
    }

    // GET Form image Avatar
    public function showAvatarForm() {
        return view('avatar-form');
    }

    // Get PROFILE and Posts from User
    public function profile(User $user) { 
        $currentlyFollowing = 0;
        if(auth()->check()) {
            // if logged in
            $currentlyFollowing = Follow::where([
                ['user_id', '=', auth()->user()->id], // you are logged in with user_id in 'follows' table
                ['followeduser', '=', $user->id] // already following the 'followeduser'
            ])->count(); // boolean
        };
        
        return view('profile-posts', [
            'username' => $user->username,
            'avatar' =>$user->avatar,
            'currentlyFollowing' => $currentlyFollowing,
            'posts' => $user->posts()->latest()->get(),
            'postCount' => $user->posts()->count()    
        ]);
    }

    // LOGOUT
    public function logout() {
        auth()->logout();
        return redirect('/')->with('successMessage', 'You are now logged out');
    }

    // REGISTER
    public function register(Request $request) {
        $incomingFields = $request->validate([
            'username' => [
                'required', 
                'min:3', 
                'max:20', 
                Rule::unique('users', 'username')        
        ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')
            ],
            'password' => [
                'required',
                'min:3',
                'confirmed'
            ]
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        // Log in user
        auth()->login($user);
        return redirect('/')->with('successMessage', 'Thank you for creating an account. You are logged in.');
    }

    // LOGIN
    public function login(Request $request) {
        $incomingFields = $request->validate([
            'loginusername' => 'required',            
            'loginpassword' => 'required'
        ]);

        if(auth()->attempt([
            'username' => $incomingFields['loginusername'],
            'password' => $incomingFields['loginpassword']
            ])) {
                    $request->session()->regenerate();
                    return redirect('/')->with('successMessage', 'You have successfully logged in');
        } else {
            return redirect('/')->with('failureMessage', 'Invalid login');
        }
    }

    // CHECK AUTH 
    public function showCorrectHomepage() {
        if(auth()->check()){
            return view('homepage-feed');
        }else {
            return view('homepage');
        }
        
    }

}
