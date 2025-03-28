<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Role::where('name', 'employee')->exists()) {
            Role::create(['id' => Str::uuid(), 'name' => 'employee']);
        }
       echo "RoleSeeder has been executed\n";
    }
}
