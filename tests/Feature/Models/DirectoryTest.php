<?php

use App\Models\Directory;

it('joins the names of five nested directories with spaces turned into plus signs', function () {
    $first = Directory::factory()->root()->create(['name' => 'Σημειώσεις Θεωρίας']);
    $second = Directory::factory()->for($first)->create(['name' => 'Παλιά Θέματα']);
    $third = Directory::factory()->for($second)->create(['name' => '2017']);
    $fourth = Directory::factory()->for($third)->create(['name' => 'Ασκήσεις']);
    $fifth = Directory::factory()->for($fourth)->create(['name' => 'Λύσεις Α & Β', 'path' => 'stale']);

    $path = $fifth->calculatePath()->path;

    expect($path)->toBe('Σημειώσεις+Θεωρίας/Παλιά+Θέματα/2017/Ασκήσεις/Λύσεις+Α+&+Β');
});

it('stops the walk at a soft-deleted parent', function () {
    $root = Directory::factory()->root()->create(['name' => 'Σημειώσεις']);
    $parent = Directory::factory()->for($root)->create(['name' => 'Παλιά Θέματα']);
    $child = Directory::factory()->for($parent)->create(['name' => 'Λύσεις Ασκήσεων']);
    $parent->delete();

    $path = $child->fresh()->calculatePath()->path;

    expect($path)->toBe('Λύσεις+Ασκήσεων');
});
