<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class NotesController extends Controller
{
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

        $cache_key_user = 'file_' . $file->md5;

        $response = Cache::store('file')->rememberForever($cache_key_user, function () use ($file) {
            $disk = Storage::disk(config('filesystems.cloud', 's3'));
            $storage_path = $file->md5;
            if (! $disk->exists($storage_path)) {
                abort(404);
            }

            $file_in_storage = $disk->get($storage_path);
            $type = $disk->mimeType($storage_path);

            $response = Response::make($file_in_storage, 200);
            $response->header("Content-Type", $type);

            return $response;
        });

        $file->total_downloads++;
        $file->total_overall++;
        $file->withoutTimestamps()->save();

        return $response;
    }
}
