<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services\WorkloadManagement;

/**
 * Description of EvenDistributionStrategy
 *
 * @author garys
 */
use App\Models\Project;

class EvenDistributionStrategy implements WorkloadStrategyInterface {

    public function distributeWorkload(Project $project) {
        // Logic for evenly distributing workload among team members
    }
}
