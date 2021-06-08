<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// 以下追加
Route::post('register/pre_check', 'Auth\RegisterController@pre_check')->name('register.pre_check');

Route::get('register/verify/{token}', 'Auth\RegisterController@showForm');

Route::post('register/main_check', 'Auth\RegisterController@mainCheck')->name('register.main_check');

Route::post('register/main_register', 'Auth\RegisterController@mainRegister')->name('register.main.registered');

Route::group(['middleware' => 'auth'], function() {

    // ユーザ関連
    Route::resource('users', 'UsersController', ['only' => ['index', 'show', 'edit', 'update']]);

    // フォロー/フォロー解除を追加
    Route::post('users/{user}/follow', 'UsersController@follow')->name('follow');
    Route::delete('users/{user}/unfollow', 'UsersController@unfollow')->name('unfollow');

    // チャット関連
    Route::resource('messages', 'MessagesController', ['only' => ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']]);

    // お気に入り
    Route::resource('favorites', 'FavoritesController', ['only' => ['store', 'destroy']]);

    // チャンネル
    Route::resource('channels', 'ChannelsController', ['only' => ['store', 'create']]);

    // チャンネル参加者
    Route::resource('joins', 'JoinsController', ['only' => ['store', 'destroy']]);

    // 検索
    Route::get('search', 'SearchController@index');

});
