<?php

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

Route::group(['middleware' => 'auth'], function () {
   
	//Rota de usuario
	Route::group(['prefix'=> 'users', 'where'=>['id'=>'0-9+']], function () {
			Route::get('', ['as' => 'users.index', 'uses' => 'UserController@index']);
			Route::get('/list',['as' => 'users.list', 'uses' => 'UserController@listar']);
			Route::post('/create', ['as' => 'users.store', 'uses' => 'UserController@store']);
			Route::post('/edit', ['as' => 'users.update', 'uses' => 'UserController@update']);
			Route::post('/delete', ['as' => 'users.destroy', 'uses' => 'UserController@destroy',]);
			Route::post('/ativar', ['as' => 'users.ativar', 'uses' => 'UserController@ativar',]);
	});

});
