<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory(2)->create();
        foreach ($users as $user) {
            $token = $user->createToken("Access Token");
            $this->command->info("Created User {$user->name}");
            $this->command->info("User Token :  {$token->plainTextToken}");
        }
        $this->command->info('User(s) Seeded ');


    }
}
