<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AiController extends Controller
{
    function index() {
        return view('ai.index');     
    }
}
