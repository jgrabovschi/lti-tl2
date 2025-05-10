<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Exception;

class NamespaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces', ['headers' => [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $dataNamespaces = json_decode($res->getBody(), true)['items'];

        return view('namespaces.index')->with('data', $dataNamespaces);
    }

    public function download()
    {
        $client = new Client([
            'verify' => false
        ]);
        $res = $client->get('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces', ['headers' => [
            'Authorization' => "Bearer " . session('token'),
            'Accept' => 'application/json',
        ]]);

        $tempFilePath = storage_path('app/temp.json');
        file_put_contents($tempFilePath, $res->getBody());

        // Return the file as a downloadable response
        return response()->download($tempFilePath, 'namespaces.json')->deleteFileAfterSend(true);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('namespaces.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string'],
        ]);

        $name = $request->input('name');


        $client = new Client([
            'verify' => false
        ]);

        try{
            $client->post('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces', [
                'headers' => [
                    'Authorization' => "Bearer " . session('token'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    "apiVersion" => "v1",
                    "kind" => "Namespace",
                    "metadata" => [
                        "name" => $name,
                    ],
                ],
            ]);
        }
        catch (Exception $e) {
            return redirect()->route('showNamespaces')->withErrors('global', 'Error creating namespace: ' . $e->getMessage());
        }

        return redirect()->route('showNamespaces')->with('success', 'Namespace ' . $request->input('name') . ' created successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($name)
    {
        $client = new Client([
            'verify' => false
        ]);

        try{
            $client->delete('https://' . session('address') . ':' . session('port') . '/api/v1/namespaces/' . $name, [
                'headers' => [
                    'Authorization' => "Bearer " . session('token'),
                    'Accept' => 'application/json',
                ],
            ]);
        }
        catch (Exception $e) {
            return redirect()->route('showNamespaces')->withErrors('global', 'Error deleting namespace: ' . $e->getMessage());
        }

        return redirect()->route('showNamespaces')->with('success', 'Deleting namespace ' . $name . '...');
    }
}
