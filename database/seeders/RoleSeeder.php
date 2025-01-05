<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $role1 = Role::create(['name' => 'admin']); 
         $role2 = Role::create(['name' => 'adminmanto']); 
         $role2 = Role::create(['name' => 'auxmanto']); 
         $role2 = Role::create(['name' => 'supervisor']); 
         $role2 = Role::create(['name' => 'calidad']); 
         $role2 = Role::create(['name' => 'auxfinca']); 


         $permissions = Permission::all();

         foreach($permissions as $permiso){
            $role1->givePermissionTo($permiso);
         }
    }
}
