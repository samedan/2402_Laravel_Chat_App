<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    // Get PROFILE and Posts from User
    public function profile(User $user) { // it looks for the 'username' in the User, is not the default 'id' based on the web.php route
        // $theUserPosts = $user->posts()->get();
        // return $theUserPosts;
        return view('profile-posts', [
            'username' => $user->username,
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
