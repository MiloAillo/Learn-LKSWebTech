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
            return response()->json($validate->errors(), 400);
        };

        User::query()->insert([
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
            "description" => $request->description,
            "password" => Hash::make($request->password)
        ]);

        return response()->json(["status" => "ok", "message" => "User successfully created"], 200);
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
            return response()->json($validate->errors(), 400);
        }

        if(!$token = Auth::guard('api')->attempt($request->only('username', 'password'))) {
            return response()->json([ "error" => "username or password is invalid", "message" => "username or password is invalid" ], 401);
        }

        return response()->json($token);
    }

    public function updatePassword(Request $request) {
        $validate = Validator::make($request->all(), [
            "username" => [ 'required', 'min:8', 'max:50', new noUppercase ],
            "password" => [ 'required', "min:8" ],
        ]);

        if($validate->fails()) {
            return response()->json($validate->errors());
        }

        $database = User::query()->where("username", $request->username)->get()->first();

        if(empty($database)) {
            return response()->json("takde user macam tu");
        };

        $passwordHashed = Hash::make($request->password);
        User::query()->where("username", $request->username)->update([ "password" => $passwordHashed ]);
        return response()->json("berhasil di update le");
    }

    public function auth() {
        return response()->json(["status" => 'ok'], 200);
    }

    public function logout() {
        Auth::guard('api')->logout();
        return response()->json(['status' => 'ok'], 200);
    }
}