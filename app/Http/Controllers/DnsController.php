<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class DnsController extends Controller
{
    public function index(): View
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/dns', ['auth' =>  [session('username'), session('password')]]);

        return view('dns.index')->with('data', json_decode($res->getBody()));
    }

    public function downloadDns()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/dns', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'DNS.json')->deleteFileAfterSend(true);

    }

    public function showDnsStatic(): View
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/dns/static', ['auth' =>  [session('username'), session('password')]]);

        return view('dns.showStatic')->with('data', json_decode($res->getBody()));
    }

    public function downloadDnsStatic()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/dns/static', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'DNSStatic.json')->deleteFileAfterSend(true);

    }

    public function AddServersDns(Request $request)
    {
        $request->validate([
            'numberOfServers' => ['required','numeric','min:1'],
        ]);
        
        $numberOfServers = $request->input('numberOfServers');
        return view('dns.addServers')->with('numberOfServers', $numberOfServers);
    }

    public function AddServersRDns(Request $request)
    {
        $rules = [];

        $servers = "";
        foreach ($request->input() as $key => $value) {
            if (Str::contains($key, 'server')) {
                
                $rules[$key] = 'required|ip'; 
                $servers .= $value . ', ';

            }
            
        }
        $servers = rtrim($servers, ', ');
        $request->validate($rules);

        $client = new Client([
            'verify' => false
        ]);

        $resServers = $client->get('https://' . session('address') . '/rest/ip/dns', ['auth' =>  [session('username'), session('password')]]);

        $resServers = json_decode($resServers->getBody());

        $servers .= ',' . $resServers->{'servers'};

        $client->post('https://' . session('address') . '/rest/ip/dns/set', ['auth' =>  [session('username'), session('password')],
                        'json' => ['servers' => $servers ]]);
        return redirect()->route('showDns')->with('success', 'Dns updated successfully!');
    }

    public function removeServerDns()
    {
        $client = new Client([
            'verify' => false
        ]);

        $resServers = $client->get('https://' . session('address') . '/rest/ip/dns', ['auth' =>  [session('username'), session('password')]]);

        $resServers = json_decode($resServers->getBody());

        $servers = $resServers->{'servers'};
        $servers = explode(',', $servers);

        return view('dns.removeServers')->with('servers', $servers);
    }

    public function removeServerRDns(Request $request)
    {
        $request->validate([
            'servers' => ['sometimes','array'],
        ]);


        $serverString = "";
        
        if($request->has('servers')){
            $servers = $request->input('servers');
        
            foreach($servers as $server){
                $serverString .= $server .', ';
            }

            $serverString = rtrim($serverString, ', ');
        }
        
        
        $client = new Client([
            'verify' => false
        ]);

        $res = $client->post('https://' . session('address') . '/rest/ip/dns/set', ['auth' =>  [session('username'), session('password')],
                        'json' => ['servers' => $serverString ]]);


        return redirect()->route('showDns')->with('success', 'Dns updated successfully!');
    }

    public function editDnsStatic(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);

        $res = $client->get('https://' . session('address') . '/rest/ip/dns/static/' . $id, ['auth' =>  [session('username'), session('password')]]);

        $res = json_decode($res->getBody());

        return view('dns.editDnsStatic')->with('DnsStatic', $res);
    }

    public function createDnsStatic()
    {
        return view('dns.createDnsStatic');
    }

    public function storeDnsStatic(Request $request)
    {

        $request->validate([
            'name' => ['required','string'],
            'address' => ['required','ip'],
        ]);

        $name = $request->input('name');
        $address = $request->input('address');

        $client = new Client([
            'verify' => false
        ]);

        $res = $client->put('https://' . session('address') . '/rest/ip/dns/static', ['auth' =>  [session('username'), session('password')],
                            'json' => ['address' => $address, 'name' => $name ]]);

        $res = json_decode($res->getBody());
        return redirect()->route('showDnsStatic')->with('success', 'Dns updated successfully!');
    }

    public function updateDnsStatic(Request $request, string $id)
    {

        $request->validate([
            'name' => ['required','string'],
            'address' => ['required','ip'],
        ]);

        $name = $request->input('name');
        $address = $request->input('address');

        $client = new Client([
            'verify' => false
        ]);

        $res = $client->patch('https://' . session('address') . '/rest/ip/dns/static/' . $id, ['auth' =>  [session('username'), session('password')],
                            'json' => ['address' => $address, 'name' => $name ]]);

        $res = json_decode($res->getBody());
        return redirect()->route('showDnsStatic')->with('success', 'Dns updated successfully!');
    }

    public function toggleDns(Request $request)
    {
        $client = new Client([
            'verify' => false
        ]);
        $request->validate([
            'toggle' => [
                'required',
                'string'
            ],
        ]);

        $toggle = $request->input('toggle');
        
        $client->post('https://' . session('address') . '/rest/ip/dns/set', ['auth' =>  [session('username'), session('password')],
                        'json' => ['allow-remote-requests' => $toggle ]]);

        return redirect()->route('showDns')->with('success', 'Data toggled successfully!');
    }

    public function toggleDnsStatic(Request $request, string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $request->validate([
            'toggle' => [
                'required',
                'string'
            ],
        ]);

        $toggle = $request->input('toggle');
        
        $res = $client->patch('https://' . session('address') . '/rest/ip/dns/static/' .$id, ['auth' =>  [session('username'), session('password')],
                        'json' => ['disabled' => $toggle ]]);

        return redirect()->route('showDnsStatic')->with('success', 'Data updated successfully!');
    }

    public function destroyDnsStatic(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->delete('https://' . session('address') . '/rest/ip/dns/static/' .$id, ['auth' =>  [session('username'), session('password')]]);

        return redirect()->route('showDnsStatic')->with('success', 'Data deleted successfully!');
    }

}
