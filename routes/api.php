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

// Route::get('/', 'VendorController@hn');
Route::get( '/', 'MainController@home' );
Route::options('/', 'MainController@home');
// Route::middleware('auth:api')->post('/broadcast/auth', 'Api\BroadcastAuthController@auth');

Route::post( 'notify', 'NotificationController@notify' )->middleware('auth:api');
Route::get('get_real_time', 'OrderController@get_real_time');
//admin routes============
Route::post('auth_user', 'Admin@auth_user');
Route::post('del_user', 'Admin@del_user')->middleware('auth:api');
Route::post('empty_wallet', 'Admin@empty_wallet')->middleware('auth:api');
Route::get('get_users', 'Admin@get_users')->middleware('auth:api');
//==================
Route::get('summary', 'VendorController@summary')->middleware('auth:api');
Route::post('setfcm', 'UserController@setfcm')->middleware('auth:api');
Route::post('login', 'UserController@login');
Route::get('logout', 'UserController@logout')->middleware('auth:api');
Route::get('policy', 'MainController@policy');
Route::get('settings', 'MainController@settings')->middleware('auth:api');
Route::get('load', 'UserController@load')->middleware('auth:api');
Route::get('load_favourites', 'UserController@load_favourites')->middleware('auth:api');
Route::get('favourite', 'UserController@favourite')->middleware('auth:api');
Route::get('remove_favourite', 'UserController@remove_favourite')->middleware('auth:api');
Route::post('registerdata', 'UserController@registerdata');
Route::post('setpassword', 'UserController@setpassword');
Route::post('register', 'UserController@register');
Route::post('reset', 'UserController@reset');
Route::post('upload', 'UserController@upload')->middleware('auth:api');

Route::post('edituser', 'UserController@edituser')->middleware('auth:api');
Route::post('resetpassword', 'UserController@resetpassword');
Route::post('passcode', 'UserController@passcode');
Route::get('home', 'MainController@home');
Route::get('page', 'MainController@page');
Route::get('search', 'MainController@search');
Route::get('searchVendor', 'MainController@searchVendor');
Route::get('vendorpage', 'MainController@vendorpage');
Route::get('vendoritems', 'MainController@vendoritems');
Route::get('vendoritem', 'MainController@vendoritem');

Route::prefix('address')->group(function () {
   Route::post('save', 'AddressController@save')->middleware('auth:api');
   Route::post('edit', 'AddressController@edit')->middleware('auth:api');
   Route::get('all', 'AddressController@all')->middleware('auth:api');
   Route::get('find', 'AddressController@find')->middleware('auth:api');
   Route::get('delete', 'AddressController@delete')->middleware('auth:api');
});
Route::prefix('reply')->group(function () {
   Route::post('save', 'ReplyController@save')->middleware('auth:api');
   Route::post('edit', 'ReplyController@edit')->middleware('auth:api');
   Route::get('all', 'ReplyController@all')->middleware('auth:api');
   Route::get('delete', 'ReplyController@delete')->middleware('auth:api');
});
Route::prefix('order')->group(function () {
   Route::post('saveOffline', 'OrderController@saveOffline')->middleware('auth:api');
   Route::post('save', 'OrderController@save')->middleware('auth:api');
   Route::get('all', 'OrderController@all')->middleware('auth:api');
   Route::get('alldelivery_find', 'OrderController@alldelivery_find')->middleware('auth:api');
   Route::get('alldelivery', 'OrderController@alldelivery')->middleware('auth:api');
   Route::get('all_my_delivery', 'OrderController@all_my_delivery')->middleware('auth:api');
   Route::get('delete', 'OrderController@delete')->middleware('auth:api');
   Route::get('paid', 'OrderController@paid')->middleware('auth:api');
   Route::get('find', 'OrderController@find')->middleware('auth:api');
   Route::get('delivery_find', 'OrderController@delivery_find')->middleware('auth:api');
   Route::get('served', 'OrderController@served')->middleware('auth:api');
   Route::get('read', 'OrderController@read')->middleware('auth:api');
   Route::get('delivery_read', 'OrderController@delivery_read')->middleware('auth:api');
   Route::get('transit', 'OrderController@transit')->middleware('auth:api');
   Route::get('delivered', 'OrderController@delivered')->middleware('auth:api');
   Route::get('rejected', 'OrderController@rejected')->middleware('auth:api');
});
Route::prefix('userorder')->group(function () {
   Route::get('all', 'UserController@orderall')->middleware('auth:api');
   Route::get('paid', 'UserController@orderpaid')->middleware('auth:api');
   Route::get('find', 'UserController@orderfind')->middleware('auth:api');
   Route::get('read', 'UserController@orderread')->middleware('auth:api');
   Route::get('rejected', 'UserController@orderrejected')->middleware('auth:api');
});
Route::prefix('transaction')->group(function () {
   Route::post('set', 'TransactionsController@set')->middleware('auth:api');
});
Route::prefix('deals')->group(function () {
   Route::post('save', 'DealsController@save')->middleware('auth:api');
   Route::post('add_item', 'DealsController@add_item')->middleware('auth:api');
   Route::get('remove_item', 'DealsController@remove_item')->middleware('auth:api');
   Route::get('all', 'DealsController@get_deals')->middleware('auth:api');
});
Route::prefix('city')->group(function () {
   Route::post('save', 'AreasController@save');
   Route::post('savearea', 'AreasController@savearea');
   Route::get('all', 'AreasController@all');
   Route::get('cities', 'AreasController@cities');
   Route::get('vendorarea', 'AreasController@vendorarea');
   Route::get('delivery', 'AreasController@delivery');
   Route::get('areas', 'AreasController@areas');
});
Route::prefix('vendor')->group(function () {
   Route::post('upload', 'VendorController@upload')->middleware('auth:api');
   Route::get('load', 'VendorController@load')->middleware('auth:api');
   Route::post('save', 'VendorController@save')->middleware('auth:api');
   Route::post('update', 'VendorController@update')->middleware('auth:api');
   Route::get('find', 'VendorController@find');
   Route::get('ordered', 'VendorController@ordered')->middleware('auth:api');
   Route::post('setfee', 'VendorController@setfee')->middleware('auth:api');
   Route::post('payset', 'VendorController@payset')->middleware('auth:api');
   Route::post('changeStatus', 'VendorController@changeStatus')->middleware('auth:api');
   Route::get('tags', 'VendorController@tags');
   Route::post('delete', 'VendorController@delete')->middleware('auth:api');
   Route::get('all', 'VendorController@all');
   Route::get('get_offline_data', 'VendorController@get_offline_data')->middleware('auth:api');
});
Route::prefix('delivery')->group(function () {
   Route::post('changeStatus', 'DeliveryController@changeStatus')->middleware('auth:api');

   Route::post('upload', 'DeliveryController@upload')->middleware('auth:api');
   Route::get('load', 'DeliveryController@load')->middleware('auth:api');
   Route::get('agents', 'DeliveryController@agents')->middleware('auth:api');
   Route::get('allvendors', 'DeliveryController@allvendors')->middleware('auth:api');
   Route::post('save', 'DeliveryController@save')->middleware('auth:api');
   Route::post('updateLocation', 'DeliveryController@updateLocation')->middleware('auth:api');
   Route::post('update', 'DeliveryController@update')->middleware('auth:api');
   Route::get('find', 'DeliveryController@find');
   Route::post('setfee', 'DeliveryController@setfee')->middleware('auth:api');
   Route::post('payset', 'DeliveryController@payset')->middleware('auth:api');
   Route::get('tags', 'DeliveryController@tags');
   Route::post('delete', 'DeliveryController@delete')->middleware('auth:api');
   Route::get('all', 'DeliveryController@all');
});

Route::prefix('review')->group(function () {
   Route::post('save', 'ReviewsController@save')->middleware('auth:api');
   Route::post('update', 'ReviewsController@update')->middleware('auth:api');
   Route::post('delete', 'ReviewsController@delete')->middleware('auth:api');
   Route::get('all', 'ReviewsController@all');
});

Route::prefix('item')->group(function () {
   Route::post('save', 'ItemController@save')->middleware('auth:api');
   Route::post('update', 'ItemController@update')->middleware('auth:api');
   Route::get('find', 'ItemController@find');
   Route::post('available', 'ItemController@available')->middleware('auth:api');
   Route::post('delete', 'ItemController@delete')->middleware('auth:api');
   Route::post('image', 'ItemController@image')->middleware('auth:api');
   Route::get('all', 'ItemController@all')->middleware('auth:api');
   Route::get('count_orders', 'ItemController@count_orders')->middleware('auth:api');
   // Route::get('status', function (Request $request) {

   //  })->middleware('auth:api');
});
Route::prefix('main_option')->group(function () {
   Route::post('save', 'MainOptionController@save')->middleware('auth:api');
   Route::post('update', 'MainOptionController@update')->middleware('auth:api');
   Route::get('find', 'MainOptionController@find');
   Route::post('available', 'MainOptionController@available')->middleware('auth:api');
   Route::post('delete', 'MainOptionController@delete')->middleware('auth:api');
   Route::get('all', 'MainOptionController@all')->middleware('auth:api');
});
Route::prefix('options')->group(function () {
   Route::post('save', 'OptionController@save')->middleware('auth:api');
   Route::post('update', 'OptionController@update')->middleware('auth:api');
   Route::post('available', 'OptionController@available')->middleware('auth:api');
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
Route::group(['middleware' => 'auth:api'], function () {
   Route::get('details', 'UserController@details');
});
