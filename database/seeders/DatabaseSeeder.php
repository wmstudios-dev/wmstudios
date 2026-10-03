<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SiteContentSeeder::class,
        ]);

        // Made-up sample works, clients and articles: only on a developer's machine.
        if (app()->environment('local')) {
            $this->call(DemoContentSeeder::class);
        }
    }
}
