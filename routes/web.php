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

Route::get('/', 'QuizController@index')->name('quiz');
Route::get('questions','QuizController@questions')->name('questions');
Route::post('probability', 'QuizController@probability')->name('probability');
Route::post('send', 'QuizController@send')->name('send');
