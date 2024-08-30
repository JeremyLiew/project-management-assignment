<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services\WorkloadManagement;

/**
 * Description of WorkloadManager
 *
 * @author garys
 */
class WorkloadManager {

    protected $strategy;

    public function __construct(WorkloadStrategyInterface $strategy) {
        $this->strategy = $strategy;
    }

    public function manage(Project $project) {
        return $this->strategy->distributeWorkload($project);
    }
}
