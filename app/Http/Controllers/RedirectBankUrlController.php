<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class RedirectBankUrlController extends Controller
{
    public function index()
    {
    	return view('agent.addmoney');
    }
}
