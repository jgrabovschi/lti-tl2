<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpKernel\Debug\VirtualRequestStack;
use GuzzleHttp\Exception\RequestException;

class WireguardController extends Controller
{
    public function showInterfaces()
    {
        $client = new Client(['verify' => false]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireguard', [
            'auth' => [session('username'), session('password')],
        ]);

        return view('wireguard.index')->with('data', $res->getBody());
    }

    public function downloadInterfaces()
    {
        $client = new Client(['verify' => false]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireguard', [
            'auth' => [session('username'), session('password')],
        ]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'interfaces-wireguard.json')->deleteFileAfterSend(true);

    }

    public function showPeers()
    {
        $client = new Client(['verify' => false]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireguard/peers', [
            'auth' => [session('username'), session('password')],
        ]);

        $qr = QrCode::size(300)->generate('https://www.google.com');

        return view('wireguard.peers')->with('peers', json_decode($res->getBody()))
            ->with('qr', $qr);
    }

    public function downloadPeers()
    {
        $client = new Client(['verify' => false]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireguard/peers', [
            'auth' => [session('username'), session('password')],
        ]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'peers-wireguard.json')->deleteFileAfterSend(true);

    }

    public function destroyPeer(string $id)
    {
        $client = new Client(['verify' => false]);
        $client->delete('https://' . session('address') . '/rest/interface/wireguard/peers/' . $id, [
            'auth' => [session('username'), session('password')],
        ]);

        return redirect()->route('showWireguardPeers');
    }

    public function createPeer()
    {
        $client = new Client(['verify' => false]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireguard', [
            'auth' => [session('username'), session('password')],
        ]);


        return view('wireguard.new-peer')->with('interfaces', json_decode($res->getBody()));
    }

    public function storePeer(Request $request)
    {
        $request->validate([
            'allowed-address' => 'required|ip',
            'client-dns' => 'required|ip',
            'client-endpoint' => 'required|ip',
            'private-key' => 'required|string',
            'interface' => 'required|string'
        ]);

        $client = new Client(['verify' => false]);
        try
        {
            $client->put('https://' . session('address') . '/rest/interface/wireguard/peers', [
                'auth' => [session('username'), session('password')],
                'json' => [
                    'allowed-address' => $request->{'allowed-address'} . '/32',
                    'private-key' => $request->{'private-key'},
                    'interface' => $request->interface,
                    'client-dns' => $request->{'client-dns'},
                    'client-endpoint' => $request->{'client-endpoint'},
                    'client-address' => $request->{'allowed-address'},
                ],
            ]);
        }
        catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }


        return redirect()->route('showWireguardPeers');
    }

    public function showQrCode(string $id)
    {
        $client = new Client(['verify' => false]);
        $res = $client->post('https://' . session('address') . '/rest/interface/wireguard/peers/show-client-config', [
            'auth' => [session('username'), session('password')],
            'json' => [
                '.id' => $id,
            ],
        ]);

        
        $peer = json_decode($res->getBody());
        
        $qr = QrCode::size(300)->generate($peer[0]->conf);

        return view('wireguard.qr')
            ->with('qr', $qr)
            ->with('peer', $peer);
    }
}
