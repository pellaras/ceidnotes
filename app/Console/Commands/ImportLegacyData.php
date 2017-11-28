<?php

namespace App\Console\Commands;

use App\File;
use App\Directory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

        $directories = DB::connection('old')->table('directories')->get();

        $total = $directories->count();

        $bar = $this->output->createProgressBar($total);

        foreach ($directories as $directory) {
            $d = Directory::withoutTimestamps()->withTrashed()->updateOrCreate(
                ['legacy_id' => $directory->dir_id],
                [
                    'directory_id' => $directory->parent_dir > 0 ? $directory->parent_dir : null,
                    'name' => $directory->dir_name,
                    'user_id' => $directory->user_id,
                    'deleted_by_user_id' => $directory->del_user,
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

        $this->info("Prepairing to import legacy data [files]...");

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
                    'md5' => $file->md5,
                    'user_id' => $file->user_id,
                    'deleted_by_user_id' => $file->del_user,
                    'deleted_at' => $file->del_date ? Carbon::createFromTimestamp($file->del_date) : null,
                    'updated_at' => $file->edit_date ? Carbon::createFromTimestamp($file->edit_date) : Carbon::createFromTimestamp($file->filedate),
                    'created_at' => Carbon::createFromTimestamp($file->filedate),
                ]
            );
            $f->calculatePath()->save();

            $bar->advance();
        }

        $bar->finish();

        $this->line("\n");

        // $this->info("Total directories: {$total}");
    }
}
