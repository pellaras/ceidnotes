<?php

it('turns spaces in a directory name into plus signs', function (string $name, string $path) {
    expect(prepair_path($name))->toBe($path);
})->with([
    'one space' => ['Παλιά Θέματα', 'Παλιά+Θέματα'],
    'consecutive spaces' => ['Παλιά  Θέματα', 'Παλιά++Θέματα'],
    'other characters kept' => ['Λύσεις & Θέματα (2017)', 'Λύσεις+&+Θέματα+(2017)'],
    'no space' => ['Ασκήσεις', 'Ασκήσεις'],
]);

it('leaves a file name untouched', function () {
    expect(prepair_path('Άσκηση 1 & 2.pdf', true))->toBe('Άσκηση 1 & 2.pdf');
});
