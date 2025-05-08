<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $profiles = Profile::all();
        return view('login.login')->with('profiles', $profiles);
    }

    public function login(Request $request)
    {
        $request->validate([
            'address' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'token' => 'nullable|string',
            'save' => 'nullable'
        ]);

        $address = $request->input('address');
        $port = $request->input('port');
        $token = $request->input('token');

        $client = new Client([
            'verify' => false
        ]);
        
        try
        {
            $res = $client->get('https://'. $address .':'. $port .'/api/v1/', [
                'connect_timeout' => 15, //in seconds
                'http_errors' => true,
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                ]
            ]);
        }
        catch (RequestException $e) {
            // if it respods with unauthorized that means that the auth params are incorect
            // else the login is corect
            if ($e->getCode() == 401)
            {
                return back()->withErrors(['global' => 'Invalid token'])->withInput();
            }
            
            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }
        catch (Exception $e)
        {
            return back()->withErrors(['global' => 'Something went wrong... Check the if the cluster is online or if the API is working.'])->withInput();
        }

        // save the login data in the session
        session([
            'address' => $request->input('address'),
            'token' => $request->input('token'),
            'port' => $request->input('port'),
        ]);

        if ($request->input('save') == 1 &&
                !Profile::where('port', $request->input('port'))
                        ->where('address', $request->input('address'))
                        ->exists())
        {
         
            Profile::create([
                'port' => $request->input('port'),
                'address' => $request->input('address'),
                'token' => $request->input('token')
            ]);
           
        }
        return redirect()->route('showDashboard');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }

    public function deleteProfile(Profile $profile)
    {
        $profile->delete();
        return redirect()->back();
    } 
    

}
