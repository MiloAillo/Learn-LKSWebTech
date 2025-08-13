<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\noUppercase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class userController extends Controller
{
    public function addUser(Request $request) {
        $validate = Validator::make($request->all(), [
            "name" => ["required", "min:8", "max:100"],
            "username" => ['required', 'min:8', 'max:50', new noUppercase, 'unique:users'],
            "email" => ['required', 'email', 'unique:users'],
            "password" => ['required', "min:8"]
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors());
        };

        User::query()->insert([
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
            "description" => $request->description,
            "password" => Hash::make($request->password)
        ]);
    }

    public function selectUsers() {
        $data = User::query()->get();
        return response()->json($data);   
    }

    public function selectUser() {
        $userId = Auth::guard('api')->user()->id;
        $data = User::query()->where("id", $userId)->get()->first();
        return response()->json($data);
    }

    public function updateUser(Request $request) {
        if($request->password) {
            return response()->json("this endpoint cannot be used for updating password");
        };

        $validate = Validator::make($request->all(), [
            "name" => ["min:8", "max:100"],
            "username" => ['min:8', 'max:50', new noUppercase, 'unique:users'],
            "email" => ['email', 'unique:users']
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors());
        };

        $userId = Auth::guard('api')->user()->id;
        $data = User::query()->where("id", $userId)->update($request->all());
        return response()->json($data);
    }

    public function deleteUser(Request $request) {
        $data = User::query()->where("id", $request->id)->delete();
        return response()->json($data);
    }

    public function login(Request $request) {
        $validate = Validator::make($request->all(), [
            "username" => ['required', 'min:8', 'max:50', new noUppercase],
            "password" => ['required', "min:8"]
        ]);
        
        if($validate->fails()) {
            return response()->json($validate->errors());
        }

        if(!$token = Auth::guard('api')->attempt($request->only('username', 'password'))) {
            return response()->json("username or password is invalid");
        }

        return $token;
    }
}