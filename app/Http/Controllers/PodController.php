<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/pods', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $dataPods = json_decode($res->getBody(), true)['items'];

        return view('pods.index')->with('pods', $dataPods);
    }

    public function download()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/pods', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'pods.json')->deleteFileAfterSend(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $client = new Client([
            'verify' => false
        ]);
        

        $res = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $namespaces = json_decode($res->getBody(), true)['items'];

        return view('pods.create')->with('namespaces', $namespaces);
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'namespace' => 'required|string',
        'containers' => 'required|json',
    ]);

    $containersInput = json_decode($request->containers, true);

    if (!is_array($containersInput) || empty($containersInput)) {
        return redirect()->back()->withErrors(['global' => 'At least one container is required.']);
    }

    // Validação de cada container individualmente
    foreach ($containersInput as $container) {
        if (empty($container['name']) || empty($container['image'])) {
            return redirect()->back()->withErrors(['global' => 'Each container must have a name and image.']);
        }
    }

    // Preparar estrutura dos containers para o manifest do Pod
    $containers = array_map(function ($container) {
        return [
            'name' => $container['name'],
            'image' => $container['image'],
            'ports' => array_map(function ($port) {
                return ['containerPort' => (int) trim($port)];
            }, $container['ports'] ?? []),
        ];
    }, $containersInput);

    $client = new \GuzzleHttp\Client(['verify' => false]);

    try {
        $client->post('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces/' . $request->namespace . '/pods', [
            'headers' => [
                'Authorization' => "Bearer " . session('token'),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'apiVersion' => 'v1',
                'kind' => 'Pod',
                'metadata' => [
                    'name' => $request->name,
                    'namespace' => $request->namespace,
                    'labels' => [
                        'app' => $request->name,
                    ],
                ],
                'spec' => [
                    'containers' => $containers,
                ],
            ],
        ]);
    } catch (\Exception $e) {
        return redirect()->route('showPods')->withErrors(['global' => 'Failed to create pod: ' . $e->getMessage()]);
    }

    return redirect()->route('showPods')->with('success', 'Pod ' . $request->name . ' created successfully');
}


   
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $namespace, string $name)
    {
        $client = new Client([
            'verify' => false
        ]);

        try {
            $client->delete('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces/' . $namespace . '/pods/' . $name, [
                'headers' => [
                    'Authorization' => "Bearer " . session('token'),
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->route('showPods')->withErrors('global', 'Failed to delete pod: ' . $e->getMessage());
        }

        return redirect()->route('showPods')->with('success', 'Deleting pod:  ' . $name . '...');
    }
}
