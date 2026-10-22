<?php

use App\Models\Directory;
use App\Models\Lesson;
use App\Models\Semester;

describe('index', function () {
    it('lists the semesters by id with a visit link to each', function () {
        $first = Semester::factory()->create(['name' => 'Β Εξάμηνο']);
        $second = Semester::factory()->create(['name' => 'Α Εξάμηνο']);

        $response = $this->get('/notes');

        $response->assertOk()
            ->assertSeeInOrder([
                'Β Εξάμηνο',
                'href="'.url("notes/{$first->id}").'"',
                'Visit',
                'Α Εξάμηνο',
                'href="'.url("notes/{$second->id}").'"',
                'Visit',
            ], false);
    });

    it('does not list soft-deleted semesters', function () {
        Semester::factory()->trashed()->create(['name' => 'Διαγραμμένο Εξάμηνο']);

        $response = $this->get('/notes');

        $response->assertOk()->assertDontSee('Διαγραμμένο Εξάμηνο');
    });

    it('renders the uppercased app name linking to the site root and a notes link in the navbar', function () {
        config(['app.name' => 'notes.example']);

        $response = $this->get('http://notes.example.test/notes');

        $response->assertOk()
            ->assertSeeInOrder([
                'href="http://notes.example.test"',
                'NOTES.EXAMPLE',
                'href="http://notes.example.test/notes"',
                'Notes',
            ], false);
    });

    it('ignores a trailing slash', function () {
        Semester::factory()->create(['name' => 'Α Εξάμηνο']);

        $response = $this->get('/notes/');

        $response->assertOk()->assertSee('Α Εξάμηνο');
    });
});

describe('show', function () {
    it('lists the lessons of a semester by id with an open link to each lesson directory', function () {
        $semester = Semester::factory()->create();
        Lesson::factory()->for($semester)->create(['name' => 'Παλιά Θέματα']);
        Lesson::factory()->for($semester)->create(['name' => 'Ασκήσεις']);

        $response = $this->get("/notes/{$semester->id}");

        $response->assertOk()
            ->assertSeeInOrder([
                'Παλιά Θέματα',
                'href="'.url('notes/%CE%A0%CE%B1%CE%BB%CE%B9%CE%AC+%CE%98%CE%AD%CE%BC%CE%B1%CF%84%CE%B1').'"',
                'Open',
                'Ασκήσεις',
                'href="'.url('notes/%CE%91%CF%83%CE%BA%CE%AE%CF%83%CE%B5%CE%B9%CF%82').'"',
                'Open',
            ], false);
    });

    it('lists only the lessons of the requested semester', function () {
        $semester = Semester::factory()->create();
        Lesson::factory()->create(['name' => 'Άλλο Μάθημα']);

        $response = $this->get("/notes/{$semester->id}");

        $response->assertOk()->assertDontSee('Άλλο Μάθημα');
    });

    it('renders only the header row for a semester without lessons', function () {
        $semester = Semester::factory()->create();

        $response = $this->get("/notes/{$semester->id}");

        $response->assertOk()
            ->assertSee('<th>Name</th>', false)
            ->assertDontSee('Open');
    });

    it('does not list soft-deleted lessons', function () {
        $semester = Semester::factory()->create();
        Lesson::factory()->for($semester)->trashed()->create(['name' => 'Διαγραμμένο Μάθημα']);

        $response = $this->get("/notes/{$semester->id}");

        $response->assertOk()->assertDontSee('Διαγραμμένο Μάθημα');
    });

    it('ignores a trailing slash', function () {
        $semester = Semester::factory()->create();
        Lesson::factory()->for($semester)->create(['name' => 'Ασκήσεις']);

        $response = $this->get("/notes/{$semester->id}/");

        $response->assertOk()->assertSee('Ασκήσεις');
    });

    it('matches a numeric id written with leading zeros or percent-encoded digits', function (string $uri) {
        $semester = Semester::factory()->create(['id' => 1]);
        Lesson::factory()->for($semester)->create(['name' => 'Ασκήσεις']);

        $response = $this->get($uri);

        $response->assertOk()->assertSee('Ασκήσεις');
    })->with([
        'leading zero' => '/notes/01',
        'percent-encoded digit' => '/notes/%31',
    ]);

    it('returns 404 for an unknown semester id', function () {
        $response = $this->get('/notes/999');

        $response->assertNotFound();
    });

    it('returns 404 for a soft-deleted semester', function () {
        $semester = Semester::factory()->trashed()->create();

        $response = $this->get("/notes/{$semester->id}");

        $response->assertNotFound();
    });

    it('leaves out a lesson that has no directory', function () {
        $semester = Semester::factory()->create();
        Lesson::factory()->for($semester)->create(['name' => 'Ασκήσεις']);
        Lesson::factory()->for($semester)->create(['name' => 'Εργαστήριο', 'directory_id' => null]);

        $response = $this->get("/notes/{$semester->id}");

        $response->assertOk()
            ->assertSee('Ασκήσεις')
            ->assertDontSee('Εργαστήριο');
    });

    it('leaves out a lesson whose directory is soft-deleted', function () {
        $semester = Semester::factory()->create();
        Lesson::factory()->for($semester)->create(['name' => 'Ασκήσεις']);
        $hidden = Lesson::factory()->for($semester)->create(['name' => 'Εργαστήριο']);
        $hidden->directory->delete();

        $response = $this->get("/notes/{$semester->id}");

        $response->assertOk()
            ->assertSee('Ασκήσεις')
            ->assertDontSee('Εργαστήριο');
    });

    it('treats an all-digit path as a semester id, never as a directory', function () {
        Directory::factory()->root()->create(['name' => '1821']);

        $response = $this->get('/notes/1821');

        $response->assertNotFound();
    });
});
