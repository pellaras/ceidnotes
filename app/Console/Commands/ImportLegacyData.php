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
use App\Phone;
use App\Like;
use App\Report;
use App\Edit;

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
                    'password_old' => bcrypt($user->password),
                    'username' => $user->username,
                    'AM' => $user->AM,
                    'registration_year' => $user->reg_year,
                    'send_results_by_email' => !! $user->mail_results,
                    'phone_notifications_start' => $user->ph_s,
                    'phone_notifications_end' => $user->ph_e,
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

        $this->info("Importing Phones...");

        $phones = DB::connection('old')->table('phones')->get();

        $total = $phones->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($phones as $phone) {
            $p = Phone::withoutTimestamps()->withTrashed()->updateOrCreate(
                ['legacy_id' => $phone->phone_id],
                [
                    'user_id' => User::withTrashed()->where('legacy_id', $phone->user_id)->first()->id,
                    'phone_number' => $phone->phone,
                    'verification_code' => $phone->code,
                    'verified' => !! $phone->ok,
                    'updated_at' => Carbon::createFromTimestamp($phone->time),
                    'created_at' => Carbon::createFromTimestamp($phone->time),
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Configure Phone Number on Users...");

        $users = DB::connection('old')->table('users')->get();

        $total = $users->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($users as $user) {
            $p = Phone::withoutTimestamps()->withTrashed()
                ->where('legacy_id', $user->phone_id)
                ->first();
            $u = User::withoutTimestamps()->withTrashed()
                ->where('legacy_id', $user->user_id)
                ->update(
                    ['phone_id' => $p ? $p->id : null]
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
                    'comment' => empty($file->comment) ? null : $file->comment,
                    'size' => $file->size,
                    // 'total_views' => $file->cview,
                    // 'total_downloads' => $file->cdown,
                    // 'total_overall' => $file->ctotal,
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

        $this->info("Importing Likes...");

        $likes = DB::connection('old')->table('votes')->get();

        $total = $likes->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($likes as $like) {
            $l = Like::withoutTimestamps()->updateOrCreate(
                ['legacy_id' => $like->user_id . '-' . $like->file_id],
                [
                    'user_id' => User::withTrashed()->where('legacy_id', $like->user_id)->first()->id,
                    'likeable_id' => File::withTrashed()->where('legacy_id', $like->file_id)->first()->id,
                    'likeable_type' => File::class,
                    'value' => $like->vote,
                    'updated_at' => Carbon::createFromTimestamp($like->time),
                    'created_at' => Carbon::createFromTimestamp($like->time),
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Importing Reports...");

        $reports = DB::connection('old')->table('reports')->get();

        $total = $reports->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($reports as $report) {
            $r = Report::withoutTimestamps()->withTrashed()->updateOrCreate(
                ['legacy_id' => $report->report_id],
                [
                    'user_id' => optional(User::withTrashed()->where('legacy_id', $report->user_id)->first())->id,
                    'email' => $report->email,
                    'reportable_id' => $report->fd_type == 'file'
                        ? File::withTrashed()->where('legacy_id', $report->fd_id)->first()->id
                        : Directory::withTrashed()->where('legacy_id', $report->fd_id)->first()->id,
                    'reportable_type' => $report->fd_type == 'file' ? File::class : Directory::class,
                    'reason' => $report->reason,
                    'updated_at' => Carbon::createFromTimestamp($report->report_date),
                    'created_at' => Carbon::createFromTimestamp($report->report_date),
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        $this->info("Importing Edits...");

        $edits = DB::connection('old')->table('modifications')->get();

        $total = $edits->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($edits as $edit) {
            $modified_data = [
                'from_legacy' => true,
            ];
            if ($edit->status != null) {
                $modified_data['status'] = $edit->status;
            }
            if ($edit->fd_name != null) {
                $modified_data['name'] = $edit->fd_name;
            }
            if ($edit->owner != null) {
                $modified_data['is_owned'] = $edit->owner;
            }
            if ($edit->labels != null) {
                $modified_data['labels'] = $edit->labels;
            }
            if ($edit->comment != null) {
                $modified_data['comment'] = $edit->comment;
            }
            if ($edit->parent_dir != null) {
                $modified_data['directory_id'] = $edit->parent_dir;
            }
            $e = Edit::withoutTimestamps()->updateOrCreate(
                ['legacy_id' => $edit->mod_id],
                [
                    'user_id' => optional(User::withTrashed()->where('legacy_id', $edit->user_id)->first())->id,
                    'editable_id' => $edit->fd_type == 'file'
                        ? File::withTrashed()->where('legacy_id', $edit->fd_id)->first()->id
                        : Directory::withTrashed()->where('legacy_id', $edit->fd_id)->first()->id,
                    'editable_type' => $edit->fd_type == 'file' ? File::class : Directory::class,
                    'modified_data' => $modified_data,
                    'updated_at' => Carbon::createFromTimestamp($edit->mod_date),
                    'created_at' => Carbon::createFromTimestamp($edit->mod_date),
                ]
            );

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");
    }
}
