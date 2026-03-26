<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\DomainCheck;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        $admin = User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Demo domains with check history
        $domains = Domain::factory(5)->create(['user_id' => $admin->id]);

        foreach ($domains as $domain) {
            // Last 24 hours of checks
            DomainCheck::factory(24)->create(['domain_id' => $domain->id]);
        }

        // Extra users with their own domains
        User::factory(3)->create()->each(function (User $user) {
            Domain::factory(rand(1, 4))
                ->create(['user_id' => $user->id])
                ->each(function (Domain $domain) {
                    DomainCheck::factory(10)->create(['domain_id' => $domain->id]);
                });
        });
    }
}
