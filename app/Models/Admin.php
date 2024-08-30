<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Models;

/**
 * Description of Admin
 *
 * @author garys
 */
use Illuminate\Database\Eloquent\Model;

class Admin extends User {

    // Example of additional properties
    protected $adminLevel;

    // Example of additional methods
    public function getAdminLevel() {
        return $this->adminLevel;
    }

    public function setAdminLevel($level) {
        $this->adminLevel = $level;
    }

    // You can also override methods from the User model if needed
    public function getRole() {
        return 'Admin';
    }
}
