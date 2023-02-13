<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Fix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all cache, generate optimized autoload file';

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

        $this->call('view:clear');
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('clear-compiled');
        shell_exec('composer dump-autoload');
    }
}
