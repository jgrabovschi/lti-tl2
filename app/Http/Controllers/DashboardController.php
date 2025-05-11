<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $client = new Client([
            'verify' => false
        ]);
        // this prepares the headers for the following requests
        $headers = [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ];
    
        $address = session('address');
        $port = session('port');
    
        try {
            $resPods = $client->get("https://$address:$port/apis/metrics.k8s.io/v1beta1/pods", ['headers' => $headers]);
        }
        catch (RequestException $e) {
        // this usualy happens when the cluster is still starting        
           return redirect()->back()->withErrors('global', 'Cluster is still starting, please wait a few moments and try again.');
        }

        $metricsPods = json_decode($resPods->getBody(), true);
    
        $client = new Client([
            'verify' => false
        ]);
        $headers = [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ];

        $address = session('address');
        $port = session('port');


        $metricsRes = $client->get("https://$address:$port/apis/metrics.k8s.io/v1beta1/nodes", ['headers' => $headers]);
        $metricsNodes = json_decode($metricsRes->getBody(), true);

 
        $nodeRes = $client->get("https://$address:$port/api/v1/nodes", ['headers' => $headers]);
        $nodes = json_decode($nodeRes->getBody(), true);

        $cpuUsage = [];
        $memoryUsage = [];

        foreach ($metricsNodes['items'] as $metricNode) {
            $nodeName = $metricNode['metadata']['name'];


            $cpuUsedNano = rtrim($metricNode['usage']['cpu'], 'n');
            $usedMillicores = (int)$cpuUsedNano / 1_000_000;

            $nodeInfo = collect($nodes['items'])->firstWhere('metadata.name', $nodeName);
            $cpuCapacityCores = $nodeInfo['status']['capacity']['cpu'] ?? '1';
            $totalMillicores = (int)$cpuCapacityCores * 1000;

            $cpuPercentage = round(($usedMillicores / $totalMillicores) * 100, 2);

            $cpuUsage[] = [
                'name' => $nodeName,
                'percentage' => $cpuPercentage,
                'used' => round($usedMillicores, 2),
            ];

     
            $memUsedKi = (int) filter_var($metricNode['usage']['memory'], FILTER_SANITIZE_NUMBER_INT);
            $memCapacityKi = (int) filter_var($nodeInfo['status']['capacity']['memory'] ?? '0', FILTER_SANITIZE_NUMBER_INT);
            $memPercentage = $memCapacityKi > 0 ? round(($memUsedKi / $memCapacityKi) * 100, 2) : 0;

            $memoryUsage[] = [
                'name' => $nodeName,
                'percentage' => $memPercentage,
                'used' => round($memUsedKi / 1024, 3),
            ];
        }

        // Return all metrics
        return view('dashboard.index')
            ->with('cpuUsage', $cpuUsage)
            ->with('memoryUsage', $memoryUsage)
            ->with('metricsPods', $metricsPods);    
    }
    

}
