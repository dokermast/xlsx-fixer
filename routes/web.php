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



Route::get('/', 'FixController@index')->name('main');

Route::post('/check', 'FixController@check')->name('check');

Route::post('/execute/{shifted}', 'FixController@execute')->name('execute');
