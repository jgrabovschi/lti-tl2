<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Symfony\Component\Console\Command\DumpCompletionCommand;

class WirelessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function enable(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $client->post('https://' . session('address') . '/rest/interface/wireless/enable',
             ['auth' =>  [session('username'), session('password')],
             'json' => ['.id' => $id]],
            );

        return redirect()->route('showInterfacesWireless');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function disable(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $client->post('https://' . session('address') . '/rest/interface/wireless/disable', 
        [
            'auth' =>  [session('username'), session('password')],
            'json' => ['.id' => $id],
        ]);

        return redirect()->route('showInterfacesWireless');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function config(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireless/' . $id, 
        [
            'auth' =>  [session('username'), session('password')],
        ]);
        
        $res2 = $client->get('https://' . session('address') . '/rest/interface/wireless/security-profiles', 
        [
            'auth' =>  [session('username'), session('password')],
        ]);

        $bands = json_decode($res->getBody())->frequency[0] == "2" ? 
            ["2ghz-b", "2ghz-onlyg", "2ghz-b/g", "2ghz-onlyn", "2ghz-b/g/n", "2ghz-g/n"]
            : ["5ghz-a", "5ghz-onlyn", "5ghz-a/n", "5ghz-a/n/ac", "5ghz-onlyac", "5ghz-n/ac"];
        
        $modes = ["alignment-only",
                    "ap-bridge",
                    "bridge",
                    "nstreme-dual-slave",
                    "station",
                    "station-bridge",
                    "station-pseudobridge",
                    "station-pseudobridge clone",
                    "station-wds",
                    "wds-slave"];

        $range1 = range(5180, 5320, 5);
        $range2 = range(5500, 5700, 5);
        
        $result = array_merge($range1, $range2);

        $frequencies = json_decode($res->getBody())->frequency[0] == "2" ? 
                    ["auto", "2412", "2417", "2422", "2427", "2432", "2437", "2442", "2447", "2452"] :
                    $result;

        return view('wireless.config')->with('wlan', json_decode($res->getBody()))
            ->with('securityProfiles', json_decode($res2->getBody()))
            ->with('bands', $bands)
            ->with('modes', $modes)
            ->with('frequencies', $frequencies);
    }


    public function showSecurityProfiles()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireless/security-profiles', 
        [
            'auth' =>  [session('username'), session('password')],
        ]);
        return view('wireless.security-profile')->with('data', $res->getBody());
    }

    //downloads the security profiles
    public function downloadSecurity()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireless/security-profiles', 
        [
            'auth' =>  [session('username'), session('password')],
        ]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'security-profiles.json')->deleteFileAfterSend(true);
       
    }


    //shows the form
    public function createSecurity()
    {
        return view('wireless.new-security');
    }


    //creates a new security profile
    public function storeSecurity(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        $client = new Client([
            'verify' => false
        ]);
        try{
            
            $client->put('https://' . session('address') . '/rest/interface/wireless/security-profiles', 
            [
                'auth' =>  [session('username'), session('password')],
                'json' => [
                    'name' => $request->input('name'),
                    'wpa2-pre-shared-key' => $request->input('password'),
                    'mode' => 'dynamic-key',
                    'authentication-types' => 'wpa2-psk',
                ],
            ]);

        } catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }

        return redirect()->route('showSecurityProfiles');
    }

    //shows the form
    public function editSecurity(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/interface/wireless/security-profiles/' . $id, 
        [
            'auth' =>  [session('username'), session('password')],
        ]);
        return view('wireless.edit-security')->with('data', json_decode($res->getBody()));
    }


    //updates the password for the security profile
    public function updateSecurity(Request $request, string $id)
    {
        $request->validate([
            'authentication-types' => 'required|string',
            'password' => 'required|string|min:8',
        ]);
        $client = new Client([
            'verify' => false
        ]);
        try{
            
            $client->patch('https://' . session('address') . '/rest/interface/wireless/security-profiles/' . $id, 
            [
                'auth' =>  [session('username'), session('password')],
                'json' => [
                    $request->input('authentication-types') == 'wpa-psk' ? 'wpa-pre-shared-key' : 'wpa2-pre-shared-key' => $request->input('password'),
                    'authentication-types' => $request->input('authentication-types'),
                ],
            ]);

        } catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }

        return redirect()->route('showSecurityProfiles');
    }


    //deletes security profile
    public function deleteSecurity(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $client->delete('https://' . session('address') . '/rest/interface/wireless/security-profiles/' . $id, 
        [
            'auth' =>  [session('username'), session('password')],
        ]);
        return redirect()->route('showSecurityProfiles');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'ssid' => 'required|string',
            'security-profile' => 'required|string',
            'band' => 'required|string',
            'mode' => 'required|string',
            'frequency' => 'required|string',
        ]);
        $client = new Client([
            'verify' => false
        ]);
        try{
            
            $client->patch('https://' . session('address') . '/rest/interface/wireless/' . $id, 
            [
                'auth' =>  [session('username'), session('password')],
                'json' => [
                    'ssid' => $request->input('ssid'),
                    'security-profile' => $request->input('security-profile'),
                    'band' => $request->input('band'),
                    'mode' => $request->input('mode'),
                    'frequency' => $request->input('frequency'),
                ],
            ]);

        } catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }

        return redirect()->route('showInterfacesWireless');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
