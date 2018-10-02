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

#Route::get('/', 'HomeController@index');
Route::get('/', 'PublicController@search');

Auth::routes();

Route::get('/box/search', 'PublicController@search')->name('search');

Route::get('/box/home', 'HomeController@index')->name('home');

Route::prefix('box')->group(function(){
    Route::get('/new', 'BoxController@newBox')->name('newBox');
    Route::get('/edit/{id}', 'BoxController@editBox')->name('editBox');
    Route::post('/new', 'BoxController@saveBox')->name('saveBox');
    Route::get('/list', 'BoxController@listBox')->name('listBox');
    Route::get('/delete/{id}', 'BoxController@deleteBox');
    Route::post('/group/add', 'BoxController@addGroup')->name('addGroup');
    Route::post('/group/addBox', 'BoxController@addBoxToGroup')->name('addBoxToGroup');
    Route::get('/group/delete/{groupId}', 'BoxController@deleteGroup')->name('deleteGroup');
    Route::post('/checkWord', 'BoxController@checkWord')->name('checkWord');
});

Route::get('/{viewUri}', 'PublicController@viewUri')->name('viewUri');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
