<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Models;

/**
 * Description of Manager
 *
 * @author garys
 */
use Illuminate\Database\Eloquent\Model;

class Manager extends User {

    // Example of additional properties
    protected $managedProjects;

    // Example of additional methods
    public function getManagedProjects() {
        return $this->managedProjects;
    }

    public function setManagedProjects($projects) {
        $this->managedProjects = $projects;
    }

    // You can also override methods from the User model if needed
    public function getRole() {
        return 'Manager';
    }
}
