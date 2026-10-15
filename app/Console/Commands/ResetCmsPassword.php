<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetCmsPassword extends Command
{
    protected $signature = 'cms:reset-password {email}';

    protected $description = 'Recover a staff account using private password prompts';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (! $user) {
            $this->error('Account not found.');

            return self::FAILURE;
        }
        $password = $this->secret('New password (at least 12 characters)');
        if (! is_string($password) || strlen($password) < 12 || $password !== $this->secret('Confirm password')) {
            $this->error('Passwords must match and contain at least 12 characters.');

            return self::FAILURE;
        }
        $user->password = $password;
        $user->remember_token = null;
        $user->save();
        $this->info('Password updated.');

        return self::SUCCESS;
    }
}
