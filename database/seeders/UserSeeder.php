<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Database\Seeders;

/**
 * Description of UserSeeder
 *
 * @author garys
 */
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder {

    public function run() {
        // Creating an Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);

        // Creating a Manager
        User::create([
            'name' => 'Manager',
            'email' => 'manager@gmail.com',
            'password' => bcrypt('manager'),
            'role' => 'manager',
        ]);
    }
}    