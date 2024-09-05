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
use App\Models\User;

class UserFactory {

    public static function createUser($type) {
        $user = new User();

        switch ($type) {
            case 'admin':
                $user->role = 'admin';
                break;
            case 'manager':
                $user->role = 'manager';
                break;
            default:
                $user->role = 'user';
                break;
        }

        return $user;
    }
}
