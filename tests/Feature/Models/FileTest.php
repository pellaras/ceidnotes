<?php

use App\Models\Directory;
use App\Models\File;

it('appends the raw file name to the path of its directory', function () {
    $directory = Directory::factory()->root()->create(['path' => 'Σημειώσεις/Παλιά+Θέματα']);
    $file = File::factory()->for($directory)->create(['name' => 'Άσκηση 1 & 2.pdf', 'path' => 'stale']);

    $path = $file->calculatePath()->path;

    expect($path)->toBe('Σημειώσεις/Παλιά+Θέματα/Άσκηση 1 & 2.pdf');
});

it('uses the path of a soft-deleted directory', function () {
    $directory = Directory::factory()->root()->trashed()->create(['path' => 'Σημειώσεις/Παλιά+Θέματα']);
    $file = File::factory()->for($directory)->create(['name' => 'Λύσεις.pdf', 'path' => 'stale']);

    $path = $file->calculatePath()->path;

    expect($path)->toBe('Σημειώσεις/Παλιά+Θέματα/Λύσεις.pdf');
});
