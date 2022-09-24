<?php

namespace App\Console\Commands;

use App\Repository\DB\UserRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AddAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:add {admin_email} {admin_password} {--name=default_admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a single admin user based on argument admin_email:password';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->withProgressBar(1, function(){
            UserRepository::createAdminUser([
                'name' => $this->option('name'),
                'email' => $this->argument('admin_email'),
                'password' => Hash::make($this->argument('admin_password'))
            ]);
        });

        return 0;
    }
}
