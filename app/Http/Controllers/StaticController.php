<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class StaticController extends Controller
{
    public function index(): View
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/route', ['auth' =>  [session('username'), session('password')]]);

        return view('statics.statics')->with('data', $res->getBody());
    }

    public function downloadStatic()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/route', ['auth' =>  [session('username'), session('password')]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'staticRoutes.json')->deleteFileAfterSend(true);

    }

    public function storeStatic(Request $request)
    {
        
        $request->validate([
            'dst-address' => [
                'required',
                'regex:/^(\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?$/'
            ],
            'gateway' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (
                        !filter_var($value, FILTER_VALIDATE_IP) && // Não é um IP válido
                        !preg_match('/^[a-zA-Z0-9.-]+$/', $value)  // Não é um hostname válido
                    ) {
                        $fail('O campo gateway deve ser um endereço IP válido ou um nome de host.');
                    }
                },
            ],
        ]);
        
        $dst = $request->input('dst-address');
        $gateway = $request->input('gateway');

        $client = new Client([
            'verify' => false
        ]);

        try{

            $res = $client->put('https://' . session('address') . '/rest/ip/route', ['auth' =>  [session('username'), session('password')],
                            'json' => ['dst-address' => $dst, 'gateway' => $gateway]]);
                            
        } catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }

        //return view('interfaces.bridges')->with('data', $res->getBody());
        return redirect()->route('showStatics')->with('success', 'Data saved successfully!');
    }
    
    public function createStatic()
    {
        
        return view('statics.createStatic');
    }

    public function editStatic(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . '/rest/ip/route/' . $id, ['auth' =>  [session('username'), session('password')]]);

        return view('statics.editStatic')->with('data', $res->getBody())->with('id', $id);
    }

    public function updateStatic(Request $request, string $id)
    {
        $request->validate([
            'dst-address' => [
                'required',
                'regex:/^(\d{1,3}\.){3}\d{1,3}(\/\d{1,2})?$/'
            ],
            'gateway' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (
                        !filter_var($value, FILTER_VALIDATE_IP) && // Não é um IP válido
                        !preg_match('/^[a-zA-Z0-9.-]+$/', $value)  // Não é um hostname válido
                    ) {
                        $fail('O campo gateway deve ser um endereço IP válido ou um nome de host.');
                    }
                },
            ],
        ]);

        $dst = $request->input('dst-address');
        $gateway = $request->input('gateway');

        $client = new Client([
            'verify' => false
        ]);

        try{

            $res = $client->patch('https://' . session('address') . '/rest/ip/route/'. $id, ['auth' =>  [session('username'), session('password')],
                            'json' => ['dst-address' => $dst, 'gateway' => $gateway]]);
                            
        } catch( RequestException $e) {
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }
        

        //return view('interfaces.bridges')->with('data', $res->getBody());
        return redirect()->route('showStatics')->with('success', 'Data updated successfully!');
    }

    public function destroyStatic(string $id)
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->delete('https://' . session('address') . '/rest/ip/route/' . $id, ['auth' =>  [session('username'), session('password')]]);

        return redirect()->route('showStatics')->with('success', 'Data deleted successfully!');
    }

}
