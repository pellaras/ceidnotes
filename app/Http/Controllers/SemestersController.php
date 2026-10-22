<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Contracts\View\View;

class SemestersController extends Controller
{
    public function index(): View
    {
        $semesters = Semester::all();

        return view('notes.index', compact('semesters'));
    }

    public function show(string $id): View
    {
        $lessons = Semester::find($id)->load('lessons.directory')->lessons;

        return view('notes.index', compact('lessons'));
    }
}
