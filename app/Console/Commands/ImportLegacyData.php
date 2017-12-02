<?php

namespace App\Console\Commands;

use App\File;
use App\Directory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Semester;
use App\Lesson;
use App\User;
use App\Label;

class ImportLegacyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Legacy Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Prepairing to import legacy data...");

        $this->info("Importing Users...");

        $users = DB::connection('old')->table('users')->get();

        $total = $users->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($users as $user) {
            $u = User::withoutTimestamps()->withTrashed()->updateOrCreate(
                ['legacy_id' => $user->user_id],
                [
                    'name' => $user->realname,
                    'email' => $user->email,
                    // 'password_old' => bcrypt($user->password),
                    'username' => $user->username,
                    'AM' => $user->AM,
                    'registration_year' => $user->reg_year,
                    'is_admin' => !! $user->admin,
                    'deleted_at' => ! $user->reg_date ? Carbon::now() : null,
                    'updated_at' => ! $user->reg_date ? Carbon::now() : Carbon::createFromTimestamp($user->reg_date),
                    'created_at' => ! $user->reg_date ? Carbon::now() : Carbon::createFromTimestamp($user->reg_date),
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Importing Labels...");

        $labels = DB::connection('old')->table('labels')->get();

        $total = $labels->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($labels as $label) {
            $l = Label::withTrashed()->updateOrCreate(
                ['legacy_id' => $label->label_id],
                [
                    'code' => $label->label_name,
                    'name' => $label->label_desc,
                    'total_files' => $label->label_count,
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Importing Directories...");

        $directories = DB::connection('old')->table('directories')->get();

        $total = $directories->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($directories as $directory) {
            $d = Directory::withoutTimestamps()->withTrashed()->updateOrCreate(
                ['legacy_id' => $directory->dir_id],
                [
                    'directory_id' => $directory->parent_dir > 0 ? $directory->parent_dir : null,
                    'name' => $directory->dir_name,
                    'user_id' => User::withTrashed()->where('legacy_id', $directory->user_id)->first()->id,
                    'deleted_by_user_id' => ! $directory->del_user ? null : User::withTrashed()->where('legacy_id', $directory->del_user)->first()->id,
                    'deleted_at' => $directory->del_date ? Carbon::createFromTimestamp($directory->del_date) : null,
                    'updated_at' => $directory->edit_date ? Carbon::createFromTimestamp($directory->edit_date) : Carbon::createFromTimestamp($directory->dir_date),
                    'created_at' => Carbon::createFromTimestamp($directory->dir_date),
                ]
            );
            $d->calculatePath()->save();

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Importing Files...");

        $files = DB::connection('old')->table('files')->get();

        $total = $files->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($files as $file) {
            $f = File::withoutTimestamps()->withTrashed()->updateOrCreate(
                ['legacy_id' => $file->file_id],
                [
                    'directory_id' => Directory::withTrashed()->where('legacy_id', $file->dir_id)->first()->id,
                    'name' => $file->filename,
                    'type' => $file->type,
                    'is_owned' => !! $file->owner,
                    'comment' => $file->comment ?? null,
                    'size' => $file->size,
                    'total_views' => $file->cview,
                    'total_downloads' => $file->cdown,
                    'total_overall' => $file->ctotal,
                    'votes_up' => $file->vote_up,
                    'votes_down' => $file->vote_down,
                    'md5' => $file->md5,
                    'user_id' => User::withTrashed()->where('legacy_id', $file->user_id)->first()->id,
                    'deleted_by_user_id' => ! $file->del_user ? null : User::withTrashed()->where('legacy_id', $file->del_user)->first()->id,
                    'deleted_at' => $file->del_date ? Carbon::createFromTimestamp($file->del_date) : null,
                    'updated_at' => $file->edit_date ? Carbon::createFromTimestamp($file->edit_date) : Carbon::createFromTimestamp($file->filedate),
                    'created_at' => Carbon::createFromTimestamp($file->filedate),
                ]
            );
            $f->calculatePath()->save();

            $file_labels = explode("|", trim($file->labels, "|"));
            $f_labels = Label::whereIn('legacy_id', $file_labels)->get()->pluck('id');
            $f->labels()->sync($f_labels);

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Importing Semesters...");

        $semesters = DB::connection('old')->table('semesters')->get();

        $total = $semesters->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($semesters as $semester) {
            $s = Semester::withTrashed()->updateOrCreate(
                ['legacy_id' => $semester->semester_id],
                [
                    'name' => $semester->semester_name,
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Importing Lessons...");

        $lessons = DB::connection('old')->table('lessons')->get();

        $total = $lessons->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($lessons as $lesson) {
            $l = Lesson::withTrashed()->updateOrCreate(
                ['legacy_id' => $lesson->lesson_id],
                [
                    'directory_id' => ! $lesson->dir_id ? null : Directory::withTrashed()->where('legacy_id', $lesson->dir_id)->first()->id,
                    'semester_id' => ! $lesson->semester_id ? null : Semester::withTrashed()->where('legacy_id', $lesson->semester_id)->first()->id,
                    'name' => $lesson->lesson_name,
                    'KM' => $lesson->KM,
                    'category' => $lesson->cat,
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");
    }
}
