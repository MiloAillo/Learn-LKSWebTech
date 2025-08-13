<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class authContoroller extends Controller
{
    public function register(Request $request) {
        $validate = Validator::make($request->all(), [
            "name" => "required|min:8",
            "email" => "required|unique:users,email",
            "password" => "required|min:8"
        ], 
        [
            "name.required" => "namanya nda boleh kosong le",
            "name.min" => "Minimal 8 karakter lee",
            "email.required" => "email nda boleh kosong le",
            "email.unique" => "email sudah dipakai"
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors());         
        }

        User::query()->insert([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)            
        ]);

        return response()->json("berhasil bang");
    }

    public function login(Request $request) {
        $validate = Validator::make($request->all(), [
            "name" => "required|min:8",
            "email" => "required|exists:users,email",
            "password" => "required|min:8"
        ], 
        [
            "name.required" => "namanya nda boleh kosong le",
            "name.min" => "Minimal 8 karakter lee",
            "email.required" => "email nda boleh kosong le",
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors());         
        }

        if(!$token = Auth::guard('api')->attempt($request->only("email", "password"))) {
            return response()->json("error leeee");
        }

        return response()->json($token);
    }

    public function myProfile() {
        $userId = Auth::guard('api')->user()->id;
        $data = User::query()->get()->where("id", $userId);
        return response()->json($data);
    }

    public function updatePassword(Request $request) {
        $userId = Auth::guard('api')->user()->id;
        User::query()->where("id", $userId)->update([
            'password' => Hash::make($request->password)
        ]);
        return response()->json('password dah di update bang');
    }

    public function logout() {
        Auth::guard('api')->logout();
    }
}