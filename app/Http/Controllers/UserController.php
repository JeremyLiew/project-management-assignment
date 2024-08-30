<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Http\Controllers;

/**
 * Description of UserController
 *
 * @author garys
 */
use App\Models\Admin;
use App\Models\Manager;
use Illuminate\Http\Request;

class UserController extends Controller {

    public function createUsers() {
        // Creating an Admin
        $admin = new Admin();
        $admin->name = 'Admin Name';
        $admin->email = 'admin@example.com';
        $admin->password = bcrypt('secret');
        $admin->setAdminLevel(5);
        $admin->save();

        // Creating a Manager
        $manager = new Manager();
        $manager->name = 'Manager Name';
        $manager->email = 'manager@example.com';
        $manager->password = bcrypt('secret');
        $manager->setManagedProjects(['Project 1', 'Project 2']);
        $manager->save();

        return response()->json(['message' => 'Users created successfully']);
    }
}
