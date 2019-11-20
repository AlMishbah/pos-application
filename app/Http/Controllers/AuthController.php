<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'status' => 400,
            ]);
        }

        $credentials = $request->only(['name','password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'unauthorized'],401);
        }
        return $this->responseToken($token);
    }

    public function responseToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'password' => 'required',
            'email' => 'required|unique:users'
            
        ]);

        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'status' => 400,
            ]);
        }

        
        $send = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

            if ($send->save()) {
                
                return response()->json(['message' => 'Successfull',200]);
                
            }

        return response()->json(['message' => 'Failed',501]);

    }

    public function update(Request $request,User $user)
    {
        
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'requred'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'status' => 400,
            ]);
        }

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = $request->input('password');

        if ($user->save()) {
            return response()->json(['message','Saved']);
        }
        return response()->json(['message','Save failed']);

    }

    public function avatar(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'image' => 'required',
            
        ]);

        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'status' => 400,
            ]);
        }
        $image = time().$request->file('image')->getClientOriginalName();

        $avatar = User::create([
            'image' => $image,
        ]);

        if ($avatar->save()) {
            $request->file('image')->move('img',$image);
            return response()->json(['message' => 'Image uploaded']);
        }
        return response()->json(['message' => 'Image not uploaded']);
    }

    public function avatarEdit(Request $request,User $user)
    {
        $validator = Validator::make($request->all(),[
            'image' => 'required',
            
        ]);

        
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->toJson(),
                'status' => 400,
            ]);
        }

        $image = time().$request->file('image')->getClientOriginalName();
        $user->image = $image;
        if ($user->save()) {
            $request->file('image')->move('img',$image);
            return response()->json('message','Image uploaded');
        }
        return response()->json('message','Upload failed');
    }

    public function destroy(User $user)
    {
        if ($user->delete()) {
            return response()->json(['message','Account delete successful']);
        }
        return response()->json(['message','Delete failed']);
    }

}
