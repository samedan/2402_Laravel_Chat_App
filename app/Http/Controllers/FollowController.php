<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    //
    public function createFollow(User $user) {
        // Check 1: you cannot follow yourself
        if($user->id == auth()->user()->id) {
            return back()->with('failureMessage', 'You cannot follow yourself');
        }
        // Check 2: you cannot follow someone already followed
        $existCheck = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();// boolean
        if($existCheck) {
            return back()->with('failureMessage', 'You are already following that user');
        }
        // After Checks : 
        $newFollow = new Follow;
        $newFollow->user_id = auth()->user()->id; // person doing the following
        $newFollow->followeduser = $user->id; // folloed user
        $newFollow->save();

        return back()->with('successMessage', 'User successfully followed.');
    }
    
    public function removeFollow(User $user) {
        Follow::where([
            ['user_id', '=', auth()->user()->id],
            ['followeduser', '=', $user->id]]
        )->delete();
        
        return back()->with('successMessage', 'User succesfully unfollowed.');


    }
}
