<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services\WorkloadManagement;

/**
 * Description of WorkloadStrategyInterface
 *
 * @author garys
 */
use App\Models\Project;

interface WorkloadStrategyInterface {

    public function distributeWorkload(Project $project);
}
