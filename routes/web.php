<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\SemestersController;
use App\Models\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $files = File::latest()->take(5)->get();
    return view('welcome', compact('files'));
});

Route::get('notes', [SemestersController::class, 'index'])->name('semesters.index');
Route::get('notes/{id}', [SemestersController::class, 'show'])->where('id', '[0-9]+')->name('semesters.show');
Route::get('notes/{path}', [NotesController::class, 'show'])->where('path', '.*')->name('notes.show');

Route::get('/home', [HomeController::class, 'index'])->name('home');
