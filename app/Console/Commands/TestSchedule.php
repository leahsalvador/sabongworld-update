<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:bot {loop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test bot betting';

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
        $counter = intval($this->argument('loop'));
        echo $counter;
        for ($i = 0; $i < $counter; $i++) {
            $this->call('betting:meron');
            $this->call('betting:wala');
        }
        return 0;
    }
}
