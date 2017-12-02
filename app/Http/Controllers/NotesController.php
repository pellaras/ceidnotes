<?php

namespace App\Http\Controllers;

use App\File;
use App\Directory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Semester;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $path
     * @return \Illuminate\Http\Response
     */
    public function show($path)
    {
        $directory = Directory::where('path', $path)
            ->with('directories', 'files')
            ->first();

        if ($directory) {
            $directories = $directory->directories;
            $files = $directory->files;

            return view('notes.index', compact('directories', 'files'));
        }

        $file = File::where('path', $path)
            ->firstOrFail();

        $storage_path = $file->md5;
        if (! Storage::cloud()->exists($storage_path)) {
            abort(404);
        }

        $file_in_storage = Storage::cloud()->get($storage_path);
        $type = Storage::cloud()->mimeType($storage_path);

        $response = Response::make($file_in_storage, 200);
        $response->header("Content-Type", $type);

        $file->total_downloads++;
        $file->total_overall++;
        $file->withoutTimestamps()->save();

        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
