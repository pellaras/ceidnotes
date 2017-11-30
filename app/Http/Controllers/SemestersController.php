<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Semester;

class SemestersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semesters = Semester::all();

        return view('notes.index', compact('semesters'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $path
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $lessons = Semester::find($id)->load('lessons.directory')->lessons;

        return view('notes.index', compact('lessons'));
    }
}
