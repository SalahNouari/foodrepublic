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

Route::get('/', function () {
   return 'success'; 
});
// Route::get('/', 'VendorController@hn');
Route::post('login', 'UserController@login');
Route::get('logout', 'UserController@logout')->middleware('auth:api');
Route::post('registerdata', 'UserController@registerdata');
Route::post('setpassword', 'UserController@setpassword');
Route::post('register', 'UserController@register');
Route::post('reset', 'UserController@reset');
Route::post('upload', 'UserController@upload')->middleware('auth:api');

Route::post('edituser', 'UserController@edituser')->middleware('auth:api');
Route::post('resetpassword', 'UserController@resetpassword');
Route::post('passcode', 'UserController@passcode');
Route::get('home', 'MainController@home');

Route::prefix('vendor')->group(function () {
   Route::post('save', 'VendorController@save')->middleware('auth:api');
   Route::post('update', 'VendorController@update')->middleware('auth:api');
   Route::get('find', 'VendorController@find');
   Route::post('delete', 'VendorController@delete')->middleware('auth:api');
   Route::get('all', 'VendorController@all');
});

Route::prefix('review')->group(function () {
   Route::post('save', 'ReviewsController@save')->middleware('auth:api');
   Route::post('update', 'ReviewsController@update')->middleware('auth:api');
   Route::get('find', 'ReviewsController@find');
   Route::post('delete', 'ReviewsController@delete')->middleware('auth:api');
   Route::get('all', 'ReviewsController@all');
});

Route::prefix('item')->group(function () {
   Route::post('save', 'ItemController@save')->middleware('auth:api');
   Route::post('update', 'ItemController@update')->middleware('auth:api');
   Route::get('find', 'ItemController@find');
   Route::post('delete', 'ItemController@delete')->middleware('auth:api');
   Route::post('image', 'ItemController@image')->middleware('auth:api');
   Route::get('all', 'ItemController@all')->middleware('auth:api');
   // Route::get('status', function (Request $request) {

   //  })->middleware('auth:api');
});
Route::prefix('options')->group(function () {
   Route::post('save', 'OptionController@save')->middleware('auth:api');
   Route::post('update', 'OptionController@update')->middleware('auth:api');
   Route::get('find', 'OptionController@find');
   Route::post('delete', 'OptionController@delete')->middleware('auth:api');
   Route::post('image', 'OptionController@image')->middleware('auth:api');
   Route::get('all', 'OptionController@all')->middleware('auth:api');
 
});
Route::prefix('category')->group(function () {
   Route::post('save', 'CategoryController@save')->middleware('auth:api');
   Route::post('update', 'CategoryController@update')->middleware('auth:api');
   Route::get('find', 'CategoryController@find');
   Route::post('delete', 'CategoryController@delete')->middleware('auth:api');
   Route::get('all', 'CategoryController@all')->middleware('auth:api');
});
Route::post('order', 'MainController@order');
Route::group(['middleware' => 'auth:api'], function()
{
   Route::get('details', 'UserController@details');
});