<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class InsertDummyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:generate-users {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates dummy user data';

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
     * @return int
     */
    public function handle()
    {
        //return 0;
        $usersData = $this->argument('count');
        for ($i = 0; $i < $usersData; $i++) { 
            User::factory()->create();
        }
    }
}
