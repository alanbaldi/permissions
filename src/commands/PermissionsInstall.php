<?php

namespace Lamplighter\Permissions;

use Exception;
use Illuminate\Console\Command;

class PermissionsInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $this->callSilent('vendor:publish',[ '--tag' => 'permissions-config','permissions-migrations']);
        $this->callSilent('vendor:publish',[ '--tag' => 'permissions-migrations']);
        $this->info('Has been installed');
        return 0;
    }
}
