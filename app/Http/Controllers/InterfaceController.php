<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class InterfaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface', ['auth' =>  [session('username'), session('password')]]);

        return view('interfaces.interfaces')->with('data', $res->getBody());
    }


    public function wireless()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireless', ['auth' =>  [session('username'), session('password')]]);

        return view('interfaces.wireless')->with('data', $res->getBody());
    }

    public function bridge()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/bridge', ['auth' =>  [session('username'), session('password')]]);

        return view('interfaces.bridges')->with('data', $res->getBody());
    }

    public function storeBridge(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'arp' => 'required|in:disabled,enabled,local-proxy-arp,proxy-arp,replay-only',
        ]);

        $name= $request->input('name');
        $arp = $request->input('arp');

        $client = new Client([
            'verify' => false
        ]);
        $res = $client->put('https://' . session('address') . '/rest/interface/bridge', ['auth' =>  [session('username'), session('password')],
                            'json' => ['name' => $name, 'arp' => $arp]]);

        //return view('interfaces.bridges')->with('data', $res->getBody());
        return redirect()->route('showInterfacesBridge')->with('success', 'Data saved successfully!');
    }

    public function createBridge()
    {
        return view('interfaces.createBridge');
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    public function editBridge(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/bridge/' . $id, ['auth' =>  [session('username'), session('password')]]);

        $resInt = $client->get('https://' . session('address') . '/rest/interface', ['auth' =>  [session('username'), session('password')]]);

        $resPort = $client->get('https://' . session('address') . '/rest/interface/bridge/port', ['auth' =>  [session('username'), session('password')]]);

        // interface dá a interface que é(ehter1,ether2,etc) e bridge dá a bridge que pertcence 
        //json_decode($data)->name
        $interfaces = json_decode($resInt->getBody());
        $ports = json_decode($resPort->getBody());
        foreach($interfaces as $interface){
            foreach($ports as $port){
                if($port->interface == $interface->name){
                    $interface->bridge = $port->bridge;
                    $interface->bridgeId = $port->{'.id'};
                }
            }
            if(!property_exists($interface,'bridge')){
                $interface->bridge = '';
            }
        }

        return view('interfaces.editBridge')->with('data', $res->getBody())->with('id', $id)->with('interfaces' , $interfaces);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function updateBridge(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string',
            'arp' => 'required|in:disabled,enabled,local-proxy-arp,proxy-arp,replay-only',
        ]);

        $name= $request->input('name');
        $arp = $request->input('arp');

        $client = new Client([
            'verify' => false
        ]);
        $res = $client->patch('https://' . session('address') . '/rest/interface/bridge/' . $id, ['auth' =>  [session('username'), session('password')],
                            'json' => [ ".id"=> $id, 'name' => $name, 'arp' => $arp]]);

        //return view('interfaces.bridges')->with('data', $res->getBody());
        return redirect()->route('showInterfacesBridge')->with('success', 'Data updated successfully!');
    }

    public function addPortBridge(Request $request)
    {
        $request->validate([
            'interface' => 'required|string',
            'bridge' => 'required|string',
        ]);

        $interface= $request->input('interface');
        $idBridged = $request->input('bridge');

        $client = new Client([
            'verify' => false
        ]);
        $res = $client->put('https://' . session('address') . '/rest/interface/bridge/port',
                ['auth' =>  [session('username'), session('password')],
                'json' => ['interface' => $interface, 'bridge' => $idBridged]]);
        

        //return view('interfaces.bridges')->with('data', $res->getBody());
        return redirect()->route('editBridge', ['id' => $idBridged])->with('success', 'Data updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function destroyBridge(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->delete('https://' . session('address') . '/rest/interface/bridge/' . $id, ['auth' =>  [session('username'), session('password')]]);

        return redirect()->route('showInterfacesBridge')->with('success', 'Data deleted successfully!');
    }

    public function destroyPortBridge(Request $request, string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->delete('https://' . session('address') . '/rest/interface/bridge/port/' . $id, ['auth' =>  [session('username'), session('password')]]);

        //return redirect()->route('showInterfacesBridge')->with('success', 'Data deleted successfully!');
        //tenho fazer o redirect para interfacebridge
        //return redirect()->route('showInterfacesBridge', ['id' => $interface])->with('success', 'Data updated successfully!');
        //este bridge vem como request 
        $request->validate([
            'bridge' => 'required|string',
        ]);
        return redirect()->route('editBridge', ['id' => $request->bridge])->with('success', 'Data deleted successfully!');
    }

    public function download()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'interfaces.json')->deleteFileAfterSend(true);

    }

    public function downloadWireless()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireless', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'wireless.json')->deleteFileAfterSend(true);

    }

    public function downloadBridge()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/bridge', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'bridge.json')->deleteFileAfterSend(true);

    }
}
