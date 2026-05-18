<?php

namespace App\Http\Controllers;

use App\Models\Semester;

class SemestersController extends Controller
{
    public function index()
    {
        $semesters = Semester::all();

        return view('notes.index', compact('semesters'));
    }

    public function show($id)
    {
        $lessons = Semester::find($id)->load('lessons.directory')->lessons;

        return view('notes.index', compact('lessons'));
    }
}
