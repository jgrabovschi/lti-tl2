<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AddressController extends Controller
{
    public function index(): View
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/address', ['auth' =>  [session('username'), session('password')]]);

        return view('addresses.index')->with('data', $res->getBody());
    }

    public function downloadAddress()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/address', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'IPAddresses.json')->deleteFileAfterSend(true);

    }

    public function createAddress()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface', ['auth' =>  [session('username'), session('password')]]);
        
        return view('addresses.createAddress')->with('bridges', json_decode($res->getBody()));
    }

    public function storeAddress(Request $request)
    {
        
        $request->validate([
            'address' => [
                'required',
                'regex:/^(\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?$/'
            ],
            'network' => [
                'required',
                'ip',
            ],
            'interface' => ['required','string'],
        ]);
        
        $address = $request->input('address');
        $network = $request->input('network');
        $interface = $request->input('interface');

        $client = new Client([
            'verify' => false
        ]);
        $res = $client->put('https://' . session('address') . '/rest/ip/address', ['auth' =>  [session('username'), session('password')],
                            'json' => ['address' => $address, 'network' => $network, 'interface' => $interface ]]);

        //return view('interfaces.bridges')->with('data', $res->getBody());
        return redirect()->route('showAddress')->with('success', 'Data saved successfully!');
    }

    public function destroyAddress(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->delete('https://' . session('address') . '/rest/ip/address/' . $id, ['auth' =>  [session('username'), session('password')]]);

        return redirect()->route('showAddress')->with('success', 'Data deleted successfully!');
    }

    public function editAddress(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/address/' . $id, ['auth' =>  [session('username'), session('password')]]);
        $resBridge = $client->get('http://' . session('address') . '/rest/interface', ['auth' =>  [session('username'), session('password')]]);
        return view('addresses.editAddress')->with('data', json_decode($res->getBody()))->with('id', $id)->with('bridges', json_decode($resBridge->getBody()));
    }

    public function updateAddress(Request $request, string $id)
    {
        $request->validate([
            'address' => [
                'required',
                'regex:/^(\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?$/'
            ],
            'network' => [
                'required',
                'ip',
            ],
            'interface' => ['required','string'],
        ]);
        

        $address = $request->input('address');
        $network = $request->input('network');
        $interface = $request->input('interface');
        
        
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->patch('https://' . session('address') . '/rest/ip/address/'. $id, ['auth' =>  [session('username'), session('password')],
                        'json' => ['address' => $address, 'network' => $network, 'interface' => $interface  ]]);
        
        return redirect()->route('showAddress')->with('success', 'Data updated successfully!');
        
        /*$client = new Client();
        $res = $client->patch('http://' . session('address') . '/rest/ip/address/'. $id, ['auth' =>  [session('username'), session('password')],
                            'json' => ['address' => $address, 'network' => $network ]]);*/

        //return view('interfaces.bridges')->with('data', $res->getBody());
        //return redirect()->route('showAddress')->with('success', 'Data updated successfully!');
    }
}
