<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//user by aldyrifaldi
Route::post('/login','AuthController@login');
Route::resource('user', 'AuthController');
Route::post('/user/image/{user}','AuthController@avatar');
Route::get('/image/{image}',function($image){
    return response()->file('img/'.$image);
});