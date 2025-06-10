<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AiController extends Controller
{
    public function index() {
        // Initialize chat if not already present in session
        if (!session()->has('chat')) {
            session()->put('chat', [
                [
                    "role" => "model",
                    "parts" => [
                        [
                            "text" => "Hi, do you have any question about Kubernetes or on how this app works?"
                        ]
                    ]
                ]
            ]);
        }
        
        return view('ai.index');     
    }

    public function storeMessage(Request $request) {
        $chat = session()->get('chat', []);
        
        // Add new message to chat
        if ($request->has('message')) {
            $chat[] = $request->input('message');
            session()->put('chat', $chat);
        }
        
        return response()->json([
            'success' => true,
            'chat' => $chat
        ]);
    }

    public function clearChat() {
        // Reset chat to initial state
        session()->put('chat', [
            [
                "role" => "model", 
                "parts" => [
                    [
                        "text" => "Hi, do you have any question about Kubernetes or on how this app works?"
                    ]
                ]
            ]
        ]);
        
        return redirect()->route('showAI');
    }
}
