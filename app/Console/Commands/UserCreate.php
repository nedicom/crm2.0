<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание нового пользователя';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = null;

        if ((User::where('email', $this->argument('email'))->first()) === null) {
            $user = User::create([
                'name' => 'ziggrun',
                'email' => $this->argument('email'),
                'password' => Hash::make($this->argument('password')),
                'avatar' => User::generateRandomAvatar(),
                'remember_token' => null,
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,
            ]);
        }

        if ($user !== null) {
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }
}
