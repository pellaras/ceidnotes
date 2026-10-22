<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SemestersController extends Controller
{
    public function index(): View
    {
        $semesters = Semester::all();

        return view('notes.index', compact('semesters'));
    }

    public function show(string $id): View
    {
        $lessons = Semester::findOrFail($id)
            ->load([
                'lessons' => fn (HasMany $query) => $query->has('directory'),
                'lessons.directory',
            ])
            ->lessons;

        return view('notes.index', compact('lessons'));
    }
}
