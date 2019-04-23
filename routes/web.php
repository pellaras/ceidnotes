<?php

use App\File;
use Illuminate\Support\Facades\Auth;
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
    $files = File::latest()->take(5)->get();
    return view('welcome', compact('files'));
});

Route::get('notes', 'SemestersController@index')->name('semesters.index');
Route::get('notes/{id}', 'SemestersController@show')->where('id', '[0-9]+')->name('semesters.show');
Route::get('notes/{path}', 'NotesController@show')->where('path', '.*')->name('notes.show');

Auth::routes(['register' => false]);

Route::get('register', 'Auth\InitiateRegistrationController@create')->name('register.initiate');
Route::post('register', 'Auth\InitiateRegistrationController@store');

Route::middleware(['signed'])->group(function() {
    Route::get('register/{username}', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register/{username}', 'Auth\RegisterController@register');
});

Route::get('/home', 'HomeController@index')->name('home');
