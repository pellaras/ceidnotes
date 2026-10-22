<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\File;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class NotesController extends Controller
{
    public function show(string $path): View|HttpResponse
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

        $cacheKey = 'file_'.$file->md5;

        $cachedFile = Cache::store('file')->rememberForever($cacheKey, function () use ($file) {
            $storagePath = $file->md5;
            if (! Storage::cloud()->exists($storagePath)) {
                abort(404);
            }

            return [
                'content' => Storage::cloud()->get($storagePath),
                'type' => Storage::cloud()->mimeType($storagePath),
            ];
        });

        $response = Response::make($cachedFile['content'], 200);
        $response->header('Content-Type', $cachedFile['type']);

        File::withoutTimestamps(fn () => $file->incrementEach([
            'total_downloads' => 1,
            'total_overall' => 1,
        ]));

        return $response;
    }
}
