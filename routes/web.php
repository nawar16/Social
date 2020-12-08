<?php

use App\Events\MyEvent;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
//Auth::routes();
/////////////////////////////////////////////////-Auth-////////////////////////////////////////////////

    Route::post('register', [ 'as' => 'register','uses' => 'UsersController@Register']);
    Route::get('login', ['as' => 'login','uses' => function () {
        return view('welcome');
    }]);
    Route::post('login', ['as' => '','uses' => 'UsersController@Login']);
    Route::post('logout', ['as' => 'logout','uses' => 'UsersController@logout']);

    
///////////////////////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', 'UsersController@dashboard')->name('dashboard');
    Route::get('/account', [
        'uses' => 'UsersController@getAccount',
        'as' => 'account'
    ]);
    
    Route::post('/updateaccount', [
        'uses' => 'UsersController@postSaveAccount',
        'as' => 'account.save'
    ]);
    
    Route::get('/userimage/{filename}', [
        'uses' => 'UsersController@getUserImage',
        'as' => 'account.image'
    ]);
    
    Route::post('/like', [
        'uses' => 'PostsController@postLikePost',
        'as' => 'like'
    ]);
    

});
///////////////////////////////////////////////////////////////////////////////////////////////////////

Route::group(['middleware' => 'auth'], function () {
    Route::resource('posts', 'PostsController', [
        'names' => [
            'index' => 'post.index',
            'show' => 'post.show',
            'create' => 'post.create',
            'store' => 'post.store',//post
            'edit' => 'post.edit',
            'update' => 'post.update', //post
            'destroy' => 'post.delete',//post
        ]
    ]);
    Route::post('posts/update', 'PostsController@update')->name('update');
    Route::get('users', 'UsersController@index')->name('users');
    Route::post('users/{user}/follow', 'UsersController@follow')->name('follow');
    Route::delete('users/{user}/unfollow', 'UsersController@unfollow')->name('unfollow');
    Route::get('/notifications', 'UsersController@notifications');
});



Route::get('take', function(){
    print_r(\Session::token());
});
