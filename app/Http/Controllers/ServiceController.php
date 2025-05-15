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

        $ports = [];
        for($i = 0; $i < $request->numberContainer; $i++){
            $ports[$i] = [];
        }
        $rules['name'] = 'required|string';
        $rules['namespace'] = 'required|string';
        $rules['selector'] = 'required|string';
        $rules['numberOfPorts'] = 'required|numeric';
        foreach ($request->input() as $key => $value) {
            if (Str::contains($key, 'namePort_')) {
                
                $rules[$key] = 'required|string'; 
                $aux = explode('_', $key);
                $ports[((int) $aux[1]) - 1]['name'] = $value;
                

            }elseif(Str::contains($key, 'protocolPort_')){
                $rules[$key] = 'required|string'; 
                $aux = explode('_', $key);
                $ports[((int) $aux[1]) -1]['protocol'] = $value;
                 
            }elseif(Str::contains($key, 'port_')){
                $rules[$key] = 'required|string'; 
                $aux = explode('_', $key);
                $ports[((int) $aux[1]) - 1]['port'] = $value;
                
            }elseif(Str::contains($key, 'targetPort_')){
                $rules[$key] = 'required|string'; 
                $aux = explode('_', $key);
                $ports[((int) $aux[1]) - 1]['targetPort'] = $value;
                
            }          

            
        }
        //dd($request);
        $request->validate($rules);

        $portsJson = array_map(function ($port) {
            return [
                'name' => $port['name'],
                'protocol' => $port['protocol'],
                'port' => (int) $port['port'],
                'targetPort' => (int) $port['targetPort'],
            ];
        }, $ports);
        
        $client = new Client([
            'verify' => false
        ]);

        try{
            $client->post('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces/' . $request->namespace . '/services', [
                'headers' => [
                    'Authorization' => "Bearer " . session('token'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'apiVersion' => 'v1',
                    'kind' => 'Service',
                    'metadata' => [
                        'name' => $request->name,
                    ],
                    'spec' => [
                        'selector' => [
                            'app' => $request->labelName //aqui é o label dos outros containers dentro do spec/matchlabels
                        ],
                        'ports' => $portsJson,
                    ],
                ],
            ]);
            
            
        }
        catch (\Exception $e) {
            dd($e->getResponse()->getBody()->getContents());
            return redirect()->route('createService')->withErrors(['global' =>  $e->getMessage()]);
        }

        return redirect()->route('showService')->with('success', 'Deployment ' . $request->input('name') . ' created successfully');
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
            $client->delete('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces/' . $namespace . '/services/' . $name, [
                'headers' => [
                    'Authorization' => "Bearer " . session('token'),
                    'Accept' => 'application/json',
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->route('showService')->withErrors('global', 'Failed to delete services: ' . $e->getMessage());
        }

        return redirect()->route('showService')->with('success', 'Deleting service:  ' . $name . '...');
    }
}
