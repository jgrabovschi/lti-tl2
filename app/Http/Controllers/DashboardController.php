<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
        $res = $client->get('https://' . session('address') . ':' . session('port') . '/apis/metrics.k8s.io/v1beta1/nodes', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $dataNodes = json_decode($res->getBody(), true);

        $res = $client->get('https://' . session('address') . ':' . session('port') . '/apis/metrics.k8s.io/v1beta1/pods', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $dataPods = json_decode($res->getBody(), true);

        return view('dashboard.index')->with('metricsNodes', $dataNodes)
            ->with('metricsPods', $dataPods);
    }

}
