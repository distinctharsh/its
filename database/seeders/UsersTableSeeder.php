<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $role = Role::where('name', 'Admin')->firstOrFail();
        User::create([
            "name"=> "Admin",
            "email"=> "superadmin@gmail.com",
            "password"=> Hash::make("password"),
            "role_id" => $role->id
        ]);
    }
}
