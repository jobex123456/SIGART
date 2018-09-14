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

Route::group(['middleware' => ['guest']], function(){
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::get('/login', 'Auth\LoginController@showLoginForm');
    Route::post('/login', 'Auth\LoginController@login')->name('login');
});

Route::group(['middleware' => ['auth']], function(){

    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('/main', 'PanelController@index')->name('main');
});



Route::get('/home', 'HomeController@index')->name('home');

Route::get('/role', 'RoleController@index');
Route::Post('/role/register', 'RoleController@store');
Route::PUT('/role/update', 'RoleController@update');
Route::Put('/role/deactivate', 'RoleController@deactivate');
Route::Put('/role/activate', 'RoleController@activate');
Route::Put('/role/delete', 'RoleController@delete');
Route::get('/role/select', 'RoleController@selectRole');

Route::get('/user', 'UserController@index');
Route::Post('/user/register', 'UserController@store');
Route::PUT('/user/update', 'UserController@update');
Route::Put('/user/deactivate', 'UserController@deactivate');
Route::Put('/user/activate', 'UserController@activate');
Route::Put('/user/delete', 'UserController@delete');

Route::get('/module', 'ModuleController@index');
Route::Post('/module/register', 'ModuleController@store');
Route::PUT('/module/update', 'ModuleController@update');
Route::Put('/module/deactivate', 'ModuleController@deactivate');
Route::Put('/module/activate', 'ModuleController@activate');
Route::Put('/module/delete', 'ModuleController@delete');
Route::get('/module/select', 'ModuleController@selectModule');


Route::get('/test', function () {
    return view('test/contenido_test');
})->name('test');