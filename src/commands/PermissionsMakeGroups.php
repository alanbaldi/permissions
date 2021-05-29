<?php

namespace Lamplighter\Permissions;

use Exception;
use Illuminate\Console\Command;

class PermissionsMakeGroups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:makegroups';

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
        try{
            $this->warn('Generating...');
            Permissions::createGroups();
            $this->info('success!!');

        }catch(Exception $e){
            $this->error($e->getMessage());
        }
        
        return 0;
    }
}
