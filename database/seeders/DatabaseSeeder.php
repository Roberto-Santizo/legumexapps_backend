<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CropSeeder::class);
        $this->call(RecipeSeeder::class);
        $this->call(FincaSeeder::class);
        // $this->call(LoteSeeder::class);

    }
}
