<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index()
    {
        $client = new Client([
            'verify' => false
        ]);
        $resServices = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/services', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $resIngress = $client->get('https://' . session('address') . ':' . session('port') . '/apis/networking.k8s.io/v1/ingresses', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $dataServices = json_decode($resServices->getBody(), true)['items'];
        $dataIngress = json_decode($resIngress->getBody(), true)['items'];
        //dd($dataIngress);
        return view('service.index')->with('services', $dataServices)->with('ingress', $dataIngress);
    }

    public function downloadService()
    {
        $client = new Client([
            'verify' => false
        ]);
        $resServices = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/services', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);


        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $resServices->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'services.json')->deleteFileAfterSend(true);
    }

    public function downloadIngress()
    {
        $client = new Client([
            'verify' => false
        ]);

        $resIngress = $client->get('https://' . session('address') . ':' . session('port') . '/apis/networking.k8s.io/v1/ingresses', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $resIngress->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'ingresses.json')->deleteFileAfterSend(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $request->validate([
            'numberOfPorts' => ['required','numeric','min:1'],
        ]);
        
        $client = new Client([
            'verify' => false
        ]);
        

        $res = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces', ['headers' => 
        [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $namespaces = json_decode($res->getBody(), true)['items'];

        return view('service.create')->with('namespaces', $namespaces)->with('numberOfPorts', $request->numberOfPorts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [];

        $containers = [];
        for($i = 0; $i < $request->numberContainer; $i++){
            $containers[$i] = [];
        }
        $rules['name'] = 'required|string';
        $rules['namespace'] = 'required|string';
        $rules['replicas'] = 'required|numeric';
        $rules['labelName'] = 'required|string';
        $rules['numberContainer'] = 'required|numeric';
        foreach ($request->input() as $key => $value) {
            if (Str::contains($key, 'nameContainer_')) {
                
                $rules[$key] = 'required|string'; 
                $aux = explode('_', $key);
                $containers[((int) $aux[1]) - 1]['name'] = $value;
                

            }elseif(Str::contains($key, 'imageContainer_')){
                $rules[$key] = 'required|string'; 
                $aux = explode('_', $key);
                $containers[((int) $aux[1]) -1]['image'] = $value;
                 
            }elseif(Str::contains($key, 'port_')){
                $rules[$key] = 'required|string'; 
                $aux = explode('_', $key);
                $containers[((int) $aux[1]) - 1]['port'] = explode(',', $value);
                
            }            

            
        }
        $request->validate($rules);

        $containersJson = array_map(function ($container) {
            return [
                'name' => $container['name'],
                'image' => $container['image'],
                'ports' => array_map(function ($port) {
                    return ['containerPort' => (int)$port];
                }, $container['port'] ?? []),
            ];
        }, $containers);
        //dd($containersJson);

        /*$request->validate([
            'name' => 'required|string',
            'namespace' => 'required|string',
            'image' => 'required|string',
            'port' => 'required|string',
            'replicas' => 'required|numeric',
            'labelName' => 'required|string',
            'numberContainer' => 'required|numeric',
        ]);*/
        //preciso name_container
        //request image
        //port
        //$ports = explode(',', $request->port);
        
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
                        'replicas' => (int) $request->replicas,
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
                                'containers' => $containersJson,
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
