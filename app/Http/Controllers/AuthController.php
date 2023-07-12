<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //Register User
    public function register(Request $request)
    {
        //validate fields
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed'
        ]);

        //create user
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        //return user & token in response
        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ]);

    }

    //Login User
    public function login(Request $request)
    {
        //validate fields
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        //attemp login
        if (!Auth::attempt($fields)) {
            return response([
                'message' => 'Invalid credentials.'
            ], 403);
        }

        //return user & token in response
        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);

    }

    //Logout User
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logout Success.'
        ];
    }

    //get user detail
    public function user()
    {
        return response([
            'user' => auth()->user()
        ],200);
    }

    //update user
    public function update(Request $request){
        $fields = $request->validate([
            'name' => 'required|string' 
        ]);

        $image = $this->saveImage($request->image,'profiles');

        auth()->user()->update([
            'name' => $fields['name'],
            'image' => $image
        ]);

        return response([
            'message' => 'User updated.',
            'user' => auth()->user()
        ],200);
    } 

}