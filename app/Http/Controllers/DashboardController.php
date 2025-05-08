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
        $res = $client->get('https://' . session('address') . ': ' . session('port') . '/apis/metrics.k8s.io/v1beta1/nodes', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $data = json_decode($res->getBody(), true);

        return view('dashboard')->with('metrics', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function download()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . ': ' . session('port') . '/apis/metrics.k8s.io/v1beta1/nodes', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'resources.json')->deleteFileAfterSend(true);
    }

}
