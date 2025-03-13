<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:initial-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create initial admin access';

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
        $user = User::create(
            [
                'name' => 'Admin',
                'email' => $this->ask('email'),
                'access_status' => User::ACCESS_STATUS_ACTIVE,
                'password' => Hash::make($this->secret('password'))
            ]
        );
        $user->assignRole('Super-Admin');

        $this->info('Super Admin has been created');
        return 0;
    }
}
