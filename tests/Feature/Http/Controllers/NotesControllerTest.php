<?php

use App\Models\Directory;
use App\Models\File;
use App\Models\Lesson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

describe('directory listing', function () {
    it('lists child directories by id before files by id, with browse and download links', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        File::factory()->for($directory)->create(['name' => 'Zeta.pdf']);
        File::factory()->for($directory)->create(['name' => 'Beta.pdf']);
        Directory::factory()->for($directory)->create(['name' => 'Omega']);
        Directory::factory()->for($directory)->create(['name' => 'Alpha']);

        $response = $this->get('/notes/Notes');

        $response->assertOk()
            ->assertSeeInOrder([
                'Omega', 'href="'.url('notes/Notes/Omega').'"', 'Browse',
                'Alpha', 'href="'.url('notes/Notes/Alpha').'"', 'Browse',
                'Zeta.pdf', 'href="'.url('notes/Notes/Zeta.pdf').'"', 'Download',
                'Beta.pdf', 'href="'.url('notes/Notes/Beta.pdf').'"', 'Download',
            ], false);
    });

    it('links a child directory with plus signs for spaces and percent-encoded Greek', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        Directory::factory()->for($directory)->create(['name' => 'Σημειώσεις Θεωρίας']);

        $response = $this->get('/notes/Notes');

        $response->assertOk()
            ->assertSee('href="'.url('notes/Notes/%CE%A3%CE%B7%CE%BC%CE%B5%CE%B9%CF%8E%CF%83%CE%B5%CE%B9%CF%82+%CE%98%CE%B5%CF%89%CF%81%CE%AF%CE%B1%CF%82').'"', false);
    });

    it('links a file by its percent-encoded raw name', function (string $name, string $href) {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        File::factory()->for($directory)->create(['name' => $name]);

        $response = $this->get('/notes/Notes');

        $response->assertOk()->assertSee('href="'.url('notes/Notes/'.$href).'"', false);
    })->with([
        'Greek with a space as %20' => ['Άσκηση 1.pdf', '%CE%86%CF%83%CE%BA%CE%B7%CF%83%CE%B7%201.pdf'],
        'plus signs kept literal' => ['C++ Notes.txt', 'C++%20Notes.txt'],
        'brackets encoded and ampersand kept' => ['Solutions (A & B).pdf', 'Solutions%20%28A%20&amp;%20B%29.pdf'],
        'percent escaped (KI-6)' => ['Progress 100%.txt', 'Progress%20100%25.txt'],
        'hash escaped (KI-6)' => ['Exercise #3.pdf', 'Exercise%20%233.pdf'],
        'question mark escaped (KI-6)' => ['What is it?.txt', 'What%20is%20it%3F.txt'],
    ]);

    it('builds absolute links from the scheme and host of the request', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        Directory::factory()->for($directory)->create(['name' => 'Old Exams']);

        $response = $this->get('http://notes.example.test/notes/Notes');

        $response->assertOk()->assertSee('href="http://notes.example.test/notes/Notes/Old+Exams"', false);
    });

    it('resolves a directory path however it is spelled', function (string $uri) {
        $directory = Directory::factory()->root()->create(['name' => 'Παλιά Θέματα']);
        File::factory()->for($directory)->create(['name' => 'Λύσεις.pdf']);

        $response = $this->get($uri);

        $response->assertOk()->assertSee('Λύσεις.pdf');
    })->with([
        'raw UTF-8 with a plus sign' => '/notes/Παλιά+Θέματα',
        'percent-encoded with a plus sign' => '/notes/%CE%A0%CE%B1%CE%BB%CE%B9%CE%AC+%CE%98%CE%AD%CE%BC%CE%B1%CF%84%CE%B1',
        'percent-encoded plus sign' => '/notes/%CE%A0%CE%B1%CE%BB%CE%B9%CE%AC%2B%CE%98%CE%AD%CE%BC%CE%B1%CF%84%CE%B1',
        'trailing slash' => '/notes/Παλιά+Θέματα/',
    ]);

    it('returns 404 when a space in a directory path is sent as %20', function () {
        Directory::factory()->root()->create(['name' => 'Παλιά Θέματα']);

        $response = $this->get('/notes/%CE%A0%CE%B1%CE%BB%CE%B9%CE%AC%20%CE%98%CE%AD%CE%BC%CE%B1%CF%84%CE%B1');

        $response->assertNotFound();
    });

    it('returns 404 for an unknown path', function () {
        $response = $this->get('/notes/'.fake()->word().'/'.fake()->word());

        $response->assertNotFound();
    });

    it('does not list soft-deleted child directories or files', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        Directory::factory()->for($directory)->trashed()->create(['name' => 'Παλιός Φάκελος']);
        File::factory()->for($directory)->trashed()->create(['name' => 'Παλιό Αρχείο.pdf']);

        $response = $this->get('/notes/Notes');

        $response->assertOk()
            ->assertDontSee('Παλιός Φάκελος')
            ->assertDontSee('Παλιό Αρχείο.pdf');
    });

    it('serves the live directory when a soft-deleted one shares its path', function () {
        $trashed = Directory::factory()->root()->trashed()->create(['name' => 'Παλιά Θέματα']);
        File::factory()->for($trashed)->create(['name' => 'Διαγραμμένος Φάκελος.pdf']);
        $live = Directory::factory()->root()->create(['name' => 'Παλιά Θέματα']);
        File::factory()->for($live)->create(['name' => 'Ενεργός Φάκελος.pdf']);

        $response = $this->get('/notes/Παλιά+Θέματα');

        $response->assertOk()
            ->assertSee('Ενεργός Φάκελος.pdf')
            ->assertDontSee('Διαγραμμένος Φάκελος.pdf');
    });

    it('returns 404 for a soft-deleted directory', function () {
        Directory::factory()->root()->trashed()->create(['name' => 'Παλιά Θέματα']);

        $response = $this->get('/notes/Παλιά+Θέματα');

        $response->assertNotFound();
    });

    it('lists the directory of a soft-deleted lesson', function () {
        $lesson = Lesson::factory()->trashed()->create(['name' => 'Παλιά Θέματα']);
        File::factory()->for($lesson->directory)->create(['name' => 'Λύσεις.pdf']);

        $response = $this->get('/notes/Παλιά+Θέματα');

        $response->assertOk()->assertSee('Λύσεις.pdf');
    });

    it('lists a live directory whose parent is soft-deleted (KI-9)', function () {
        $parent = Directory::factory()->root()->create(['name' => 'Notes']);
        $child = Directory::factory()->for($parent)->create(['name' => 'Old Exams']);
        File::factory()->for($child)->create(['name' => 'Λύσεις.pdf']);
        $parent->delete();

        $response = $this->get('/notes/Notes/Old+Exams');

        $response->assertOk()->assertSee('Λύσεις.pdf');
    });

    it('shows the directory when a directory and a file share a path, leaving the file unreachable', function () {
        $parent = Directory::factory()->root()->create(['name' => 'Notes']);
        $file = File::factory()->for($parent)->create(['name' => 'Tables', 'total_downloads' => 7, 'total_overall' => 9]);
        $directory = Directory::factory()->for($parent)->create(['name' => 'Tables']);
        File::factory()->for($directory)->create(['name' => 'Λύσεις.pdf']);

        $response = $this->get('/notes/Notes/Tables');

        $response->assertOk()->assertSee('Λύσεις.pdf');
        $this->assertDatabaseHas('files', ['id' => $file->id, 'total_downloads' => 7, 'total_overall' => 9]);
    });

    it('leaves the counters of listed files unchanged', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        $file = File::factory()->for($directory)->create(['total_views' => 30, 'total_downloads' => 7, 'total_overall' => 9]);

        $response = $this->get('/notes/Notes');

        $response->assertOk();
        $this->assertDatabaseHas('files', ['id' => $file->id, 'total_views' => 30, 'total_downloads' => 7, 'total_overall' => 9]);
    });
});

describe('file download', function () {
    it('returns the blob bytes with the content type the disk reports', function (string $contents, string $contentType) {
        $file = File::factory()->create();
        Storage::fake(config('filesystems.cloud'))->put($file->md5, $contents);

        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk()
            ->assertStreamedContent($contents)
            ->assertHeader('Content-Type', $contentType);
    })->with([
        'a pdf' => ["%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF\n", 'application/pdf'],
        'a png' => [base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='), 'image/png'],
    ]);

    it('sends application/octet-stream when the disk reports no mime type', function () {
        $file = File::factory()->create();
        Storage::fake(config('filesystems.cloud'))->put($file->md5, 'Σημειώσεις για τις ασκήσεις');

        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk()
            ->assertStreamedContent('Σημειώσεις για τις ασκήσεις')
            ->assertHeader('Content-Type', 'application/octet-stream');
    });

    it('sends the file inline and uncacheable', function () {
        $file = File::factory()->create();
        Storage::fake(config('filesystems.cloud'))->put($file->md5, fake()->paragraph());

        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk()
            ->assertHeaderMissing('Content-Disposition')
            ->assertHeader('Cache-Control', 'no-cache, private');
    });

    it('sends the content length of the file', function () {
        $file = File::factory()->create();
        Storage::fake(config('filesystems.cloud'))->put($file->md5, 'Σημειώσεις για τις ασκήσεις');

        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk()->assertHeader('Content-Length', strlen('Σημειώσεις για τις ασκήσεις'));
    });

    it('keeps one local copy of the blob and its content type per md5', function () {
        $first = File::factory()->create();
        $second = File::factory()->create(['md5' => $first->md5]);
        Storage::fake(config('filesystems.cloud'))->put($first->md5, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF\n");

        $this->get(route('notes.show', $first->path))->assertOk();
        $this->get(route('notes.show', $second->path))->assertOk();

        expect(Storage::disk('downloads')->allFiles())->toEqualCanonicalizing([$first->md5, $first->md5.'.type'])
            ->and(Storage::disk('downloads')->get($first->md5))->toBe("%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF\n")
            ->and(Storage::disk('downloads')->get($first->md5.'.type'))->toBe('application/pdf');
    });

    it('adds one to both download counters per download, cache hits included, without touching updated_at', function () {
        $this->travelTo('2017-11-29 10:00:00');
        $file = File::factory()->create(['total_views' => 30, 'total_downloads' => 7, 'total_overall' => 9]);
        Storage::fake(config('filesystems.cloud'))->put($file->md5, fake()->paragraph());
        $this->travelTo('2026-10-22 12:00:00');

        $this->get(route('notes.show', $file->path))->assertOk();
        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk();
        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'total_views' => 30,
            'total_downloads' => 9,
            'total_overall' => 11,
            'updated_at' => '2017-11-29 10:00:00',
        ]);
    });

    it('counts a download with one atomic update of both counters', function () {
        $file = File::factory()->create();
        Storage::fake(config('filesystems.cloud'))->put($file->md5, fake()->paragraph());
        DB::enableQueryLog();

        $this->get(route('notes.show', $file->path))->assertOk();

        $updates = collect(DB::getQueryLog())
            ->pluck('query')
            ->filter(fn (string $query) => str_starts_with($query, 'update "files"'))
            ->values();
        expect($updates)->toHaveCount(1)
            ->and($updates->first())
            ->toContain('"total_downloads" = "total_downloads" + 1')
            ->toContain('"total_overall" = "total_overall" + 1')
            ->not->toContain('updated_at');
    });

    it('serves a downloaded file from its local copy after its blob is deleted', function () {
        $file = File::factory()->create();
        $disk = Storage::fake(config('filesystems.cloud'));
        $disk->put($file->md5, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF\n");
        $this->get(route('notes.show', $file->path))->assertOk();
        $disk->delete($file->md5);

        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk()
            ->assertStreamedContent("%PDF-1.4\n1 0 obj\n<<>>\nendobj\ntrailer\n<<>>\n%%EOF\n")
            ->assertHeader('Content-Type', 'application/pdf');
    });

    it('serves a file from the local copy of another file with the same md5', function () {
        $first = File::factory()->create();
        $second = File::factory()->create(['md5' => $first->md5, 'total_downloads' => 7, 'total_overall' => 9]);
        $disk = Storage::fake(config('filesystems.cloud'));
        $disk->put($first->md5, 'Σημειώσεις για τις ασκήσεις');
        $this->get(route('notes.show', $first->path))->assertOk();
        $disk->delete($first->md5);

        $response = $this->get(route('notes.show', $second->path));

        $response->assertOk()->assertStreamedContent('Σημειώσεις για τις ασκήσεις');
        $this->assertDatabaseHas('files', ['id' => $second->id, 'total_downloads' => 8, 'total_overall' => 10]);
    });

    it('returns 404 without counting a download when the blob is missing', function () {
        $file = File::factory()->create(['total_downloads' => 7, 'total_overall' => 9]);
        Storage::fake(config('filesystems.cloud'));

        $response = $this->get(route('notes.show', $file->path));

        $response->assertNotFound();
        $this->assertDatabaseHas('files', ['id' => $file->id, 'total_downloads' => 7, 'total_overall' => 9]);
    });

    it('serves a blob uploaded after a 404 because the 404 leaves no local copy', function () {
        $file = File::factory()->create();
        $disk = Storage::fake(config('filesystems.cloud'));
        $this->get(route('notes.show', $file->path))->assertNotFound();
        $disk->put($file->md5, 'Σημειώσεις για τις ασκήσεις');

        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk()->assertStreamedContent('Σημειώσεις για τις ασκήσεις');
    });

    it('serves the live file when a soft-deleted file shares its path', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        $trashed = File::factory()->for($directory)->trashed()->create(['name' => 'Λύσεις.txt']);
        $live = File::factory()->for($directory)->create(['name' => 'Λύσεις.txt']);
        $disk = Storage::fake(config('filesystems.cloud'));
        $disk->put($trashed->md5, 'Διαγραμμένες λύσεις');
        $disk->put($live->md5, 'Ενεργές λύσεις');

        $response = $this->get('/notes/Notes/Λύσεις.txt');

        $response->assertOk()->assertStreamedContent('Ενεργές λύσεις');
    });

    it('returns 404 for soft-deleted files', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        $files = File::factory()->for($directory)->trashed()->count(2)->create(['name' => 'Λύσεις.txt']);
        $disk = Storage::fake(config('filesystems.cloud'));
        $files->each(fn (File $file) => $disk->put($file->md5, fake()->paragraph()));

        $response = $this->get('/notes/Notes/Λύσεις.txt');

        $response->assertNotFound();
    });

    it('downloads a live file inside a soft-deleted directory (KI-9)', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        $file = File::factory()->for($directory)->create(['name' => 'Λύσεις.txt']);
        Storage::fake(config('filesystems.cloud'))->put($file->md5, 'Σημειώσεις για τις ασκήσεις');
        $directory->delete();

        $response = $this->get('/notes/Notes/Λύσεις.txt');

        $response->assertOk()->assertStreamedContent('Σημειώσεις για τις ασκήσεις');
    });

    it('returns 404 when a space in a file name is sent as a plus sign', function () {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        $file = File::factory()->for($directory)->create(['name' => 'Άσκηση 1.pdf']);
        Storage::fake(config('filesystems.cloud'))->put($file->md5, fake()->paragraph());

        $response = $this->get('/notes/Notes/Άσκηση+1.pdf');

        $response->assertNotFound();
    });

    it('downloads a file through its escaped link (KI-6)', function (string $name, string $uri) {
        $directory = Directory::factory()->root()->create(['name' => 'Notes']);
        $file = File::factory()->for($directory)->create(['name' => $name]);
        Storage::fake(config('filesystems.cloud'))->put($file->md5, 'Σημειώσεις για τις ασκήσεις');

        $response = $this->get($uri);

        $response->assertOk()->assertStreamedContent('Σημειώσεις για τις ασκήσεις');
    })->with([
        'percent as %25' => ['Progress 100%.txt', '/notes/Notes/Progress%20100%25.txt'],
        'hash as %23' => ['Exercise #3.txt', '/notes/Notes/Exercise%20%233.txt'],
        'question mark as %3F' => ['What is it?.txt', '/notes/Notes/What%20is%20it%3F.txt'],
    ]);

    it('returns 404 without counting a download for a file below an all-digit directory path', function () {
        $directory = Directory::factory()->root()->create(['name' => '1821']);
        $file = File::factory()->for($directory)->create(['name' => 'Χρονολόγιο.txt', 'total_downloads' => 7, 'total_overall' => 9]);
        Storage::fake(config('filesystems.cloud'))->put($file->md5, 'Σημειώσεις για τις ασκήσεις');

        $response = $this->get('/notes/1821/Χρονολόγιο.txt');

        $response->assertNotFound();
        $this->assertDatabaseHas('files', ['id' => $file->id, 'total_downloads' => 7, 'total_overall' => 9]);
    });

    it('returns 404 for a directory below an all-digit directory path', function () {
        $directory = Directory::factory()->root()->create(['name' => '1821']);
        Directory::factory()->for($directory)->create(['name' => 'Πηγές']);

        $response = $this->get('/notes/1821/Πηγές');

        $response->assertNotFound();
    });

    it('serves a path whose first segment only starts with digits', function () {
        $directory = Directory::factory()->root()->create(['name' => '1821 Επανάσταση']);
        $file = File::factory()->for($directory)->create(['name' => 'Χρονολόγιο.txt']);
        Storage::fake(config('filesystems.cloud'))->put($file->md5, 'Σημειώσεις για τις ασκήσεις');

        $response = $this->get(route('notes.show', $file->path));

        $response->assertOk()->assertStreamedContent('Σημειώσεις για τις ασκήσεις');
    });
});
