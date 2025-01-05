<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'create ot']);
        Permission::create(['name' => 'create documentocp']);
        Permission::create(['name' => 'create documentold']);
    }
}
