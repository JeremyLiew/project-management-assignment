<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services;

/**
 * Description of XmlService
 *
 * @author garys
 */
use SimpleXMLElement;

class XmlService {

    public function generateProjectXml($project) {
        $xml = new SimpleXMLElement('<project/>');
        $xml->addChild('name', $project->name);
        $xml->addChild('description', $project->description);

        $tasks = $xml->addChild('tasks');
        foreach ($project->tasks as $task) {
            $taskXml = $tasks->addChild('task');
            $taskXml->addChild('name', $task->name);
            $taskXml->addChild('status', $task->status);
        }

        return $xml->asXML();
    }
}
