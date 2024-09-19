<?php

// app/Strategies/TransformXMLStrategy.php
namespace App\Strategies;

use DOMDocument;
use XSLTProcessor;

class TransformXMLStrategy implements StrategyInterface
{
    public function execute($data)
    {
        $xml = $data['xml'];
        $xsl = new DOMDocument();
        $xsl->load(public_path('xslt/dashboard.xsl')); // Ensure the path is correct
        $processor = new XSLTProcessor();
        $processor->importStylesheet($xsl);
        return $processor->transformToXml($xml);
    }
}
