<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class DeploymentController extends Controller
{
    public function index()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . ':' . session('port') . '/apis/apps/v1/deployments', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $dataDeployment = json_decode($res->getBody(), true)['items'];

        return view('deployment.index')->with('deploys', $dataDeployment);
    }

    public function download()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . ':' . session('port') . '/apis/apps/v1/deployments', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'deployments.json')->deleteFileAfterSend(true);
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

        return view('deployment.create')->with('namespaces', $namespaces);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'namespace' => 'required|string',
            'image' => 'required|string',
            'port' => 'required|string',
            'replicas' => 'required|integer',
            'labelName' => 'required|string',
        ]);
        //preciso deployment à mesma
        $ports = explode(',', $request->port);
    
        $client = new Client([
            'verify' => false
        ]);
        try{
            $client->post('https://' . session('address') . ':' . session('port') . '/apis/apps/v1/namespaces/' . $request->namespace . '/deployments', [
                'headers' => [
                    'Authorization' => "Bearer " . session('token'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'apiVersion' => 'apps/v1',
                    'kind' => 'Deployment',
                    'metadata' => [
                        'name' => $request->name,
                    ],
                    'spec' => [
                        'replicas' => $request->replicas,
                        'selector' => [
                            'matchLabels' => [
                                'app' => $request->labelName
                            ],
                        ],
                        'template' => [
                            'metadata' => [
                                'labels' => [
                                    'app' => $request->labelName 
                                ],
                            ],
                            'spec' => [
                                'containers' =>[
                                    [
                                        'name' => $request->name,
                                        'image' => $request->image,
                                        'ports' => array_map(function ($port) {
                                            return ['containerPort' => (int)$port];
                                        }, $ports),
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
            
            
        }
        catch (\Exception $e) {
            
            return redirect()->route('createDeployment')->withErrors(['global' =>  $e->getMessage()]);
        }

        return redirect()->route('showDeployment')->with('success', 'Deployment ' . $request->input('name') . ' created successfully');
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
            $client->delete('https://' . session('address') . ':' . session('port') . '/apis/apps/v1/namespaces/' . $namespace . '/deployments/' . $name, [
                'headers' => [
                    'Authorization' => "Bearer " . session('token'),
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->route('showDeployment')->withErrors('global', 'Failed to delete deployment: ' . $e->getMessage());
        }

        return redirect()->route('showDeployment')->with('success', 'Deleting deployment:  ' . $name . '...');
    }
}
