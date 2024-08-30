<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Services;

/**
 * Description of XPathService
 *
 * @author garys
 */
use DOMDocument;
use DOMXPath;

class XPathService {

    public function queryTasksByStatus($xml, $status) {
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $xpath = new DOMXPath($dom);
        $query = "//task[status='{$status}']";
        $entries = $xpath->query($query);

        $results = [];
        foreach ($entries as $entry) {
            $results[] = [
                'name' => $entry->getElementsByTagName('name')->item(0)->nodeValue,
                'status' => $entry->getElementsByTagName('status')->item(0)->nodeValue,
            ];
        }

        return $results;
    }
}
