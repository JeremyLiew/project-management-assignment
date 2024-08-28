<?php

namespace App\Http\Controllers;

use App\Models\Log;
use DOMDocument;
use DOMNode;
use DOMXPath;
use Illuminate\Http\Request;
use XSLTProcessor;

class LogController extends Controller
{
    public function index(Request $request)
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

        $xpath = new DOMXPath($xml);
        $query = "//log";

        if ($request->filled('user')) {
            $userFilter = $request->input('user');
            $query .= "[contains(translate(user, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), translate('$userFilter', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'))]";
        }

        if ($request->filled('created_at')) {
            $createdAtFilter = $request->input('created_at');
            $query .= "[substring(created_at, 1, 10) = '$createdAtFilter']";
        }

        if ($request->filled('action')) {
            $actionFilter = $request->input('action');
            $query .= "[translate(action, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') = translate('$actionFilter', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')]";
        }


        $filteredLogs = $xpath->query($query);

        $filteredXml = new DOMDocument('1.0', 'UTF-8');
        $filteredLogsElement = $filteredXml->createElement('logs');

        foreach ($filteredLogs as $node) {
            $importedNode = $filteredXml->importNode($node, true);
            $filteredLogsElement->appendChild($importedNode);
        }

        $filteredXml->appendChild($filteredLogsElement);

        $xsl = new DOMDocument();
        $xsl->load(storage_path('app/public/logs.xsl'));

        $processor = new XSLTProcessor();
        $processor->importStylesheet($xsl);

        $actionToCount = $request->input('action', 'created');

        if($actionToCount == ''){
            $actionToCount = 'created';
        }

        $processor->setParameter('', 'actionToCount', $actionToCount);

        $htmlOutput = $processor->transformToXml($filteredXml);

        return view('logs.index', compact('logs', 'htmlOutput'));
    }
}
