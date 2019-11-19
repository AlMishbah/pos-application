<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
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
        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'unauthorized'],401);
        }
        return $this->responseToken($token);
    }

    public function responseToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function create(Request $request)
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

    public function edit(Request $request,User $user)
    {
        $validator = Validator::make($request->all(),[
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
    }

}
