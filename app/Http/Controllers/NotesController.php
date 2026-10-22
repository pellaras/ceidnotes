<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\File;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotesController extends Controller
{
    public function show(string $path): View|StreamedResponse
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

        $downloadsDisk = Storage::disk('downloads');

        if (! $downloadsDisk->exists($file->md5)) {
            if (! Storage::cloud()->exists($file->md5)) {
                abort(404);
            }

            $partialPath = $file->md5.'.'.Str::random(16).'.part';
            $downloadsDisk->writeStream($partialPath, Storage::cloud()->readStream($file->md5));
            $downloadsDisk->put($file->md5.'.type', Storage::cloud()->mimeType($file->md5) ?: 'application/octet-stream');
            $downloadsDisk->move($partialPath, $file->md5);
        }

        File::withoutTimestamps(fn () => $file->incrementEach([
            'total_downloads' => 1,
            'total_overall' => 1,
        ]));

        return Response::stream(function () use ($downloadsDisk, $file) {
            $stream = $downloadsDisk->readStream($file->md5);
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $downloadsDisk->get($file->md5.'.type'),
            'Content-Length' => $downloadsDisk->size($file->md5),
        ]);
    }
}
