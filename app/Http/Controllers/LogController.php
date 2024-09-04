<?php
// Jeremy
namespace App\Http\Controllers;

use App\Http\Requests\Log\LogFilterRequest;
use App\Models\Log;
use DOMDocument;
use DOMXPath;
use XSLTProcessor;

class LogController extends Controller
{
    public function index(LogFilterRequest $request)
    {
        $validatedData = $request->validated();

        $logs = Log::with('user')
                   ->latest()
                   ->get();

        $xml = new DOMDocument('1.0', 'UTF-8');
        $logsElement = $xml->createElement('logs');

        foreach ($logs as $log) {
            $logElement = $xml->createElement('log');

            $logElement->appendChild($xml->createElement('id', $log->id ?? 'N/A'));
            $logElement->appendChild($xml->createElement('action', htmlspecialchars($log->action ?? 'N/A')));
            $logElement->appendChild($xml->createElement('model_type', htmlspecialchars($log->model_type ?? 'N/A')));
            $logElement->appendChild($xml->createElement('model_id', $log->model_id ?? 'N/A'));
            $logElement->appendChild($xml->createElement('user', htmlspecialchars(optional($log->user)->name ?? 'N/A')));
            $logElement->appendChild($xml->createElement('changes', htmlspecialchars(json_encode(json_decode($log->changes), JSON_PRETTY_PRINT) ?? 'N/A')));
            $logElement->appendChild($xml->createElement('log_level', htmlspecialchars($log->log_level ?? 'N/A')));
            $logElement->appendChild($xml->createElement('ip_address', $log->ip_address ?? 'N/A'));
            $logElement->appendChild($xml->createElement('created_at', $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A'));

            $logsElement->appendChild($logElement);
        }

        $xml->appendChild($logsElement);

        $xpath = new DOMXPath($xml);
        $query = "//log";

        if (!empty($validatedData['user'])) {
            $userFilter = strtolower($validatedData['user']);
            $query .= "[contains(translate(user, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '$userFilter')]";
        }

        if (!empty($validatedData['created_at'])) {
            $createdAtFilter = $validatedData['created_at'];
            $query .= "[substring(created_at, 1, 10) = '$createdAtFilter']";
        }

        if (!empty($validatedData['action'])) {
            $actionFilter = strtolower($validatedData['action']);
            $query .= "[translate(action, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') = '$actionFilter']";
        }

        if (!empty($validatedData['log_level'])) {
            $logLevelFilter = strtolower($validatedData['log_level']);
            $query .= "[translate(log_level, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz') = '$logLevelFilter']";
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
        $xsl->load(public_path('xslt/logs.xsl'));

        $processor = new XSLTProcessor();
        $processor->importStylesheet($xsl);

        $actionToCount = $validatedData['action'] ?? 'created';
        if ($actionToCount == '') {
            $actionToCount = 'created';
        }
        $processor->setParameter('', 'actionToCount', $actionToCount);

        $htmlOutput = $processor->transformToXml($filteredXml);

        return view('logs.index', compact('logs', 'htmlOutput'));
    }
}

