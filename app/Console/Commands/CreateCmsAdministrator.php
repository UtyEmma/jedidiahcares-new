<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateCmsAdministrator extends Command
{
    protected $signature = 'cms:create-admin';

    protected $description = 'Create an active CMS administrator using private password prompts';

    public function handle(): int
    {
        $name = $this->ask('Administrator name');
        $email = $this->ask('Administrator email');
        $password = $this->secret('Password (at least 12 characters)', fallback: false);
        $confirmation = $this->secret('Confirm password', fallback: false);
        $validator = Validator::make(compact('name', 'email', 'password') + ['password_confirmation' => $confirmation], [
            'name' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:12|confirmed',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }
        User::create(compact('name', 'email', 'password') + ['role' => 'admin', 'is_active' => true]);
        $this->info('Administrator created. Sign in at /admin.');

        return self::SUCCESS;
    }
}
