<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates (or refreshes) the admin account from ADMIN_EMAIL / ADMIN_PASSWORD / ADMIN_NAME.
 * Locally it falls back to admin@wmspace.test / "password". In production a password must be set
 * in the environment, otherwise no account is created.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', app()->environment('local') ? 'admin@wmspace.test' : null);
        $password = env('ADMIN_PASSWORD', app()->environment('local') ? 'password' : null);

        if (! $email || ! $password) {
            $this->command?->warn('AdminSeeder skipped: set ADMIN_EMAIL and ADMIN_PASSWORD to create the admin account.');

            return;
        }

        $user = User::firstOrNew(['email' => $email]);
        $user->name = $user->name ?: env('ADMIN_NAME', 'Admin');
        $user->password = Hash::make($password);
        $user->forceFill(['is_admin' => true, 'email_verified_at' => $user->email_verified_at ?? now()])->save();

        $this->command?->info("Admin account ready: {$email}");
    }
}
