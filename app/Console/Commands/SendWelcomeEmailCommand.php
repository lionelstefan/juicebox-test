<?php

namespace App\Console\Commands;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Console\Command;

class SendWelcomeEmailCommand extends Command
{
    protected $signature = 'email:send-welcome {user_id}';
    protected $description = 'Send welcome email to a specific user by user ID';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);

        if (!$user) {
            $this->error('User not found.');
            return 1;
        }

        SendWelcomeEmail::dispatch($user);

        $this->info('Welcome email dispatched for user: ' . $user->email);
        return 0;
    }
}