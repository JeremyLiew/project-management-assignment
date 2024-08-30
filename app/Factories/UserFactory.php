<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Factories;

/**
 * Description of UserFactory
 *
 * @author garys
 */
use App\Models\Admin;
use App\Models\Manager;
use App\Models\User;

class UserFactory {

    public static function createUser($type) {
        switch ($type) {
            case 'admin':
                return new Admin();
            case 'manager':
                return new Manager();
            default:
                return new User();
        }
    }
}
