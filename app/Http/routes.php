<?php

Route::group(['middleware' => 'lang_middleware'], function()
{
    Route::resource('/songs', 'SongsController',  ['except' => ['show']]);
    
    Route::get('/', 'SongsController@index');//route
    Route::post('/language', array ( 
            'as'=>'language-chooser',
            'uses'=>'LanguageController@chooser'    
        ));
    // login Register Routes...
    Route::post('/register', 'Auth\AuthController@postregister');
    Route::get('/login', 'Auth\AuthController@showLoginForm');//route
    Route::get('/register', 'Auth\AuthController@showRegistrationForm');
    Route::post('/login', 'Auth\AuthController@postLogin');
    Route::get ('/logout','Auth\AuthController@logout');
 
    // Password Reset Routes...
    Route::get('/password/reset/{token?}', 'Auth\PasswordController@showResetForm');
    Route::post('/password/email', 'Auth\PasswordController@sendResetLinkEmail');
    Route::post('/password/reset', 'Auth\PasswordController@reset');

    Route::get('/auth/activate','Auth\AuthController@activate');
    
    Route::get('albums/create', [
        'uses'=>'AlbumsController@create',
        'as'=>'albums.create'
    ])->middleware('admin');

    Route::post('/albums/store', [
        'uses' => 'AlbumsController@store',
        'as' => 'albums.store'
    ]);

    Route::group(['prefix'=>'api', 'middleware'=>'auth:api'], function(){
	Route::resource('playlists', 'PlaylistController');
    });
    
    Route::get('callback', 'LoginFacebookController@callback');//route
});    
        