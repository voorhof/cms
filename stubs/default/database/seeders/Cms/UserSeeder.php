<?php

namespace Database\Seeders\Cms;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a super-admin user
        User::create([
            'name' => 'Super User',
            'email' => 'super@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('super-admin');

        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('admin');

        // Create a manager user
        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('manager');

        // Create an editor user
        User::create([
            'name' => 'Editor User',
            'email' => 'editor@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('editor');

        // Create a contributor user
        User::create([
            'name' => 'Contributor User',
            'email' => 'contributor@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ])->assignRole('contributor');

        // Create fake factory subscriber users.
        User::factory(10)->subscriber()->create();

        User::factory(3)->unverified()->create()->each(function ($user) {
            $user->assignRole('subscriber');
        });

        User::factory(2)->deleted()->create()->each(function ($user) {
            $user->assignRole('subscriber');
        });
    }
}
