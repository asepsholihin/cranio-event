<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionConfigSynch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:config-synch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Permission With Config';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->createSuperAdminRole();

        foreach (config('platform-permission') as $permission => $name) {
            try {
                Permission::create(['name'=>$permission]);
            } catch (InvalidArgumentException $e) {
                # nothing to do keep continue if permission already exists
            }
        }

        $this->info('Permissions has been created');
        return 0;
    }

    private function createSuperAdminRole()
    {
        try {
            Role::create(['name' => 'Super-Admin']);
        } catch (InvalidArgumentException $e) {
            # nothing to do keep continue if permission already exists
        }
    }
}
