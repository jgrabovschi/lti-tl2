<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\RequestException;

class DhcpController extends Controller
{

    public function index(): View
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('http://' . session('address') . '/rest/ip/dhcp-server', ['auth' =>  [session('username'), session('password')]]);

        return view('dhcp.index')->with('data', json_decode($res->getBody()));
    }

    public function indexPool(): View
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/pool', ['auth' =>  [session('username'), session('password')]]);

        return view('dhcp.indexPool')->with('data', json_decode($res->getBody()));
    }

    public function downloadDhcpPool()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/pool', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'DhcpPoll.json')->deleteFileAfterSend(true);

    }

    public function downloadDhcp()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/dhcp-server', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'DhcpServer.json')->deleteFileAfterSend(true);

    }



    public function createDhcp()
    {

        $client = new Client([
            'verify' => false
        ]);

        $resInt = $client->get('https://' . session('address') . '/rest/interface', ['auth' =>  [session('username'), session('password')]]);
        $interfacesName = [];
        $interfaces = json_decode($resInt->getBody());
        foreach($interfaces as $interface){
            
            $interfacesName[] = $interface->name;
        }

        $resPool = $client->get('https://' . session('address') . '/rest/ip/pool', ['auth' =>  [session('username'), session('password')]]);
        $poolName = [];

        $pools = json_decode($resPool->getBody());

        foreach($pools as $pool){
            
            $poolName[] = $pool->name;
        }
        
        return view('dhcp.create')->with('interfaces', $interfacesName)->with('pools',$poolName);

    }


    public function createDhcpPool()
    {
        return view('dhcp.createPool');

    }

    public function editDhcpPool(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);

        $res = $client->get('https://' . session('address') . '/rest/ip/pool/' . $id, ['auth' =>  [session('username'), session('password')]]);

        $res = json_decode($res->getBody());

        $ranges = $res->{'ranges'};
        $ranges = explode('-', $ranges);

        return view('dhcp.editPool')->with('dhcpPool', $res)->with('rangeBegin', $ranges[0])->with('rangeEnd', $ranges[1]);
    }

    public function editDhcp(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);

        $res = $client->get('https://' . session('address') . '/rest/ip/dhcp-server/' . $id, ['auth' =>  [session('username'), session('password')]]);

        $res = json_decode($res->getBody());

        $resInt = $client->get('https://' . session('address') . '/rest/interface', ['auth' =>  [session('username'), session('password')]]);
        $interfacesName = [];
        $interfaces = json_decode($resInt->getBody());
        foreach($interfaces as $interface){
            
            $interfacesName[] = $interface->name;
        }

        $resPool = $client->get('https://' . session('address') . '/rest/ip/pool', ['auth' =>  [session('username'), session('password')]]);
        $poolName = [];

        $pools = json_decode($resPool->getBody());

        foreach($pools as $pool){
            
            $poolName[] = $pool->name;
        }

        return view('dhcp.edit')->with('dhcp', $res)->with('interfaces', $interfacesName)->with('pools',$poolName);
    }

    public function storeDhcpPool(Request $request)
    {

        $request->validate([
            'name' => ['required','string'],
            'rangeBegin' => ['required','ip'],
            'rangeEnd' => ['required','ip'],
        ]);

        $name = $request->input('name');
        $ranges = $request->input('rangeBegin') . '-' . $request->input("rangeEnd");

        $client = new Client([
            'verify' => false
        ]);

        $res = $client->put('https://' . session('address') . '/rest/ip/pool', ['auth' =>  [session('username'), session('password')],
                            'json' => ['name' => $name, 'ranges' => $ranges ]]);
        
        return redirect()->route('showDhcpPool')->with('success', 'DHCP updated successfully!');

    }

    public function storeDhcp(Request $request)
    {

        $request->validate([
            'name' => ['required','string'],
            'interface' => ['required','string'],
            'address-pool' => ['required','string'],
        ]);

        $name = $request->input('name');
        $interface = $request->input('interface');
        $pool = $request->input('address-pool');
        $lease = '30m';

        $client = new Client([
            'verify' => false
        ]);

        try{

            $res = $client->put('https://' . session('address') . '/rest/ip/dhcp-server', ['auth' =>  [session('username'), session('password')],
                            'json' => ['name' => $name, 'interface' => $interface, 'address-pool' => $pool, 'lease-time' => $lease ]]);

        } catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }
        return redirect()->route('showDhcp')->with('success', 'DHCP created successfully!');

    }

    public function updateDhcp(Request $request, string $id)
    {

        $request->validate([
            'name' => ['required','string'],
            'interface' => ['required','string'],
            'address-pool' => ['required','string'],
        ]);

        $name = $request->input('name');
        $interface = $request->input('interface');
        $pool = $request->input('address-pool');

        $client = new Client([
            'verify' => false
        ]);
        try{

            $res = $client->patch('https://' . session('address') . '/rest/ip/dhcp-server/' . $id, ['auth' =>  [session('username'), session('password')],
                            'json' => ['name' => $name, 'interface' => $interface, 'address-pool' => $pool ]]);
                            
        } catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }
        

        return redirect()->route('showDhcp')->with('success', 'DHCP updated successfully!');

    }

    public function updateDhcpPool(Request $request, string $id)
    {

        $request->validate([
            'name' => ['required','string'],
            'rangeBegin' => ['required','ip'],
            'rangeEnd' => ['required','ip'],
        ]);

        $name = $request->input('name');
        $ranges = $request->input('rangeBegin') . '-' . $request->input("rangeEnd");

        $client = new Client([
            'verify' => false
        ]);

        $res = $client->patch('https://' . session('address') . '/rest/ip/pool/' .$id, ['auth' =>  [session('username'), session('password')],
                            'json' => ['name' => $name, 'ranges' => $ranges ]]);

        return redirect()->route('showDhcpPool')->with('success', 'DHCP updated successfully!');

    }

    public function destroyDhcpPool(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->delete('https://' . session('address') . '/rest/ip/pool/' .$id, ['auth' =>  [session('username'), session('password')]]);

        return redirect()->route('showDhcpPool')->with('success', 'Data deleted successfully!');
    }

    public function destroyDhcp(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->delete('https://' . session('address') . '/rest/ip/dhcp-server/' .$id, ['auth' =>  [session('username'), session('password')]]);

        return redirect()->route('showDhcp')->with('success', 'Data deleted successfully!');
    }
}
