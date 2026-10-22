<?php

namespace Database\Seeders;

use App\Models\Directory;
use App\Models\Edit;
use App\Models\File;
use App\Models\Label;
use App\Models\Lesson;
use App\Models\Like;
use App\Models\Phone;
use App\Models\Report;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(4)->create();
        $admin = $users->first();
        $admin->update(['is_admin' => true]);

        $users->take(2)->each(function (User $user) {
            $phone = Phone::factory()->for($user)->create();
            $user->update(['phone_id' => $phone->id]);
        });

        $labels = Label::factory(3)->create();

        Semester::factory()->trashed()->create(['name' => 'Επιλογής Χειμερινού']);

        foreach (['1ο Εξάμηνο', '2ο Εξάμηνο', '3ο Εξάμηνο'] as $semesterName) {
            $semester = Semester::factory()->create(['name' => $semesterName]);

            Lesson::factory(2)->for($semester)->recycle($users)->create()->each(function (Lesson $lesson) use ($users, $admin, $labels) {
                $root = $lesson->directory;

                $theory = $this->directory($root, 'Σημειώσεις Θεωρίας', $users);
                $exercises = $this->directory($root, 'Ασκήσεις', $users);
                $chapter = $this->directory($theory, 'Κεφάλαιο 1', $users);
                $part = $this->directory($chapter, 'Μέρος Β', $users);

                $trashedDirectory = $this->directory($root, 'Παλιά Θέματα', $users);

                $this->file($root, 'Ύλη εξετάσεων.txt', $users);
                $this->file($theory, 'Θεωρία Κεφάλαιο 1.pdf', $users)->labels()->attach($labels->first());
                $this->file($exercises, 'Σετ ασκήσεων 1.zip', $users)->labels()->attach($labels->get(1));
                $this->file($part, 'Λυμένες ασκήσεις μέρος Β.pdf', $users);
                $this->file($chapter, fake()->words(3, true).'.jpg', $users);

                collect([
                    $this->file($trashedDirectory, 'Θέματα Ιουνίου.pdf', $users),
                    $this->file($exercises, 'Σετ ασκήσεων 2.rar', $users),
                    $trashedDirectory,
                ])->each(function (Directory|File $model) use ($admin) {
                    $model->update(['deleted_by_user_id' => $admin->id]);
                    $model->delete();
                });
            });
        }

        $files = File::all();

        $users->each(fn (User $user) => Like::factory()->for($user)->create([
            'likeable_id' => $files->random()->id,
        ]));

        Report::factory()->for($users->last())->create(['reportable_id' => $files->random()->id]);
        Report::factory()->create([
            'user_id' => null,
            'email' => fake()->safeEmail(),
            'reportable_id' => Directory::query()->whereNotNull('directory_id')->value('id'),
            'reportable_type' => Directory::class,
        ]);

        Edit::factory()->for($users->first())->create(['editable_id' => $files->random()->id]);
    }

    private function directory(Directory $parent, string $name, Collection $users): Directory
    {
        return Directory::factory()->for($parent, 'directory')->recycle($users)->create(['name' => $name]);
    }

    private function file(Directory $directory, string $name, Collection $users): File
    {
        return File::factory()->for($directory)->recycle($users)->create([
            'name' => $name,
            'type' => pathinfo($name, PATHINFO_EXTENSION),
        ]);
    }
}
