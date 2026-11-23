<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Creates the admin account, or resets its password, from ADMIN_EMAIL and ADMIN_PASSWORD in .env.
// deploy.sh runs this when ADMIN_PASSWORD has changed. The password is never printed.
Artisan::command('afroiso:sync-admin', function () {
    $email = config('afroiso.admin_email');
    $password = config('afroiso.admin_password');

    if (! $email || ! $password) {
        $this->error('ADMIN_EMAIL or ADMIN_PASSWORD is empty in .env. Nothing was changed.');

        return 1;
    }

    $user = User::firstOrNew(['email' => $email]);
    $user->name = $user->name ?: 'Administrator';
    $user->password = Hash::make($password);
    $user->role = 'admin';
    $user->is_active = true;
    $user->save();

    $this->info('Admin login updated for ' . $email . '.');

    return 0;
})->purpose('Create the admin account or reset its password from .env');
