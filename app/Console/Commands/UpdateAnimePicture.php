<?php

namespace App\Console\Commands;

use App\Jobs\AnimeStoreJob;
use App\Models\Film\Film;
use Illuminate\Console\Command;

class UpdateAnimePicture extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-anime-picture';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update-anime-picture';

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

    }
}
