<?php

namespace App\Http\Controllers;

use App\Models\Log;
use DOMDocument;
use Illuminate\Http\Request;
use XSLTProcessor;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::with('user')->latest()->get();

        $xml = new DOMDocument('1.0', 'UTF-8');
        $logsElement = $xml->createElement('logs');

        foreach ($logs as $log) {
            $logElement = $xml->createElement('log');

            $logElement->appendChild($xml->createElement('id', $log->id));
            $logElement->appendChild($xml->createElement('action', htmlspecialchars($log->action)));
            $logElement->appendChild($xml->createElement('model_type', htmlspecialchars($log->model_type)));
            $logElement->appendChild($xml->createElement('model_id', $log->model_id));
            $logElement->appendChild($xml->createElement('user', htmlspecialchars(optional($log->user)->name ?? 'N/A')));
            $logElement->appendChild($xml->createElement('changes', htmlspecialchars(json_encode(json_decode($log->changes), JSON_PRETTY_PRINT))));
            $logElement->appendChild($xml->createElement('created_at', $log->created_at->format('Y-m-d H:i:s')));

            $logsElement->appendChild($logElement);
        }

        $xml->appendChild($logsElement);

        $xsl = new DOMDocument;
        $xsl->load(storage_path('app/public/logs.xsl'));

        $processor = new XSLTProcessor();
        $processor->importStylesheet($xsl);

        $htmlOutput = $processor->transformToXml($xml);

        return view('logs.index', compact('logs', 'htmlOutput'));
    }
}
