<?php

namespace Lamplighter\Permissions;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PermissionsGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate permissions app';

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
            DB::beginTransaction();
            Permissions::truncate();
            $this->info('Clean all permissions table');
            Permissions::make();
            $this->info('success!!');
            DB::commit();
            return 0;
        }catch(Exception $e){
            DB::rollBack();
            $this->error($e->getMessage());
            return 0;
        }
        return 0;
    }
}
