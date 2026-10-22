<?php

use App\Models\Directory;
use App\Models\File;

it('lists the five newest files on the home page, newest first, skipping soft-deleted ones', function () {
    $this->travelTo('2026-10-22 12:00:00');
    $directory = Directory::factory()->root()->create(['name' => 'Σημειώσεις']);
    File::factory()->for($directory)->create(['name' => 'Παλαιότερο.pdf', 'created_at' => now()->subDays(7)]);
    File::factory()->for($directory)->create(['name' => 'Πέμπτο.pdf', 'created_at' => now()->subDays(6)]);
    File::factory()->for($directory)->create(['name' => 'Τέταρτο.pdf', 'created_at' => now()->subDays(5)]);
    File::factory()->for($directory)->create(['name' => 'Τρίτο.pdf', 'created_at' => now()->subDays(4)]);
    File::factory()->for($directory)->create(['name' => 'Δεύτερο.pdf', 'created_at' => now()->subDays(3)]);
    File::factory()->for($directory)->create(['name' => 'Πρώτο.pdf', 'created_at' => now()->subDays(2)]);
    File::factory()->for($directory)->trashed()->create(['name' => 'Διαγραμμένο.pdf', 'created_at' => now()->subDay()]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSeeInOrder(['Πρώτο.pdf', 'Δεύτερο.pdf', 'Τρίτο.pdf', 'Τέταρτο.pdf', 'Πέμπτο.pdf'])
        ->assertDontSee('Παλαιότερο.pdf')
        ->assertDontSee('Διαγραμμένο.pdf');
});

it('shows each recent file with its age and a download link', function () {
    $this->travelTo('2026-10-22 12:00:00');
    $directory = Directory::factory()->root()->create(['name' => 'Σημειώσεις']);
    File::factory()->for($directory)->create(['name' => 'Άσκηση 1.pdf', 'created_at' => now()->subDays(2)]);

    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Άσκηση 1.pdf <span>[2 days ago]</span>', false)
        ->assertSee('href="'.url('notes/%CE%A3%CE%B7%CE%BC%CE%B5%CE%B9%CF%8E%CF%83%CE%B5%CE%B9%CF%82/%CE%86%CF%83%CE%BA%CE%B7%CF%83%CE%B7%201.pdf').'"', false);
});

it('returns 404 for an unknown top-level path', function () {
    $response = $this->get('/'.fake()->slug());

    $response->assertNotFound();
});

it('returns 404 for the dropped dashboard and api routes (KI-5)', function (string $uri) {
    $response = $this->get($uri);

    $response->assertNotFound();
})->with([
    'dashboard' => '/home',
    'api user' => '/api/user',
]);

it('returns 400 for a path that is not valid UTF-8 once decoded', function (string $uri) {
    $response = $this->get($uri);

    $response->assertBadRequest();
})->with([
    'truncated greek' => '/notes/%CE',
    'invalid byte' => '/notes/%FF',
]);
